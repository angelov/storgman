<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of Storgman.
 *
 * Storgman is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Storgman is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Storgman
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Core\Repositories;

use Angelov\Storgman\Core\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository implements RepositoryInterface
{
    protected $entity;

    public function __construct(Model $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity(Model $entity)
    {
        $this->entity = $entity;
    }

    public function all(array $withRelationships = [])
    {
        return $this->entity->with($withRelationships)->get()->all();
    }

    public function get($id)
    {
        $resource = $this->entity->find($id);

        if ($resource == null) {
            throw new ResourceNotFoundException();
        }

        return $resource;
    }

    public function destroy($id)
    {
        $resource = $this->get($id);

        if ($resource instanceof Model) {
            $resource->delete();
        }
    }

    public function getByPage($page = 1, $limit = 20, array $withRelationships = [])
    {
        $results = new \stdClass();
        $results->page = $page;
        $results->limit = $limit;
        $results->totalItems = 0;
        $results->items = array();

        // This will show a warning in the IDEs, but that's
        // because the QueryBuilder uses __call()
        $fetched = $this->entity->with($withRelationships)
            ->orderBy('id', 'desc')
            ->skip($limit * ($page - 1))
            ->take($limit)
            ->get()->all();

        $results->totalItems = $this->countAll();
        $results->items = $fetched;

        return $results;
    }

    public function latest($count, array $withRelationships = [], $orderByField = 'date')
    {
        $fetched = $this->entity->with($withRelationships)
            ->orderBy($orderByField, 'desc')
            ->take($count)
            ->get()->all();

        return $fetched;
    }

    public function getByIds(array $ids = [])
    {
        $query = $this->entity->newQuery();
        $results = $query->findMany($ids)->all();

        return $results;
    }

    public function countAll()
    {
        $eloquentQuery = $this->entity->newQuery();
        $queryBuilder = $eloquentQuery->getQuery();

        return $queryBuilder->count();
    }
}
