<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of EESTEC Platform.
 *
 * EESTEC Platform is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EESTEC Platform is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EESTEC Platform
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Repository;

use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $withRelationships = [])
    {
        return $this->model->with($withRelationships)->get()->all();
    }

    public function get($id)
    {
        $resource = $this->model->find($id);

        if ($resource == null) {
            throw new ResourceNotFoundException();
        }

        return $resource;
    }

    public function destroy($id)
    {
        $resource = $this->get($id);
        $resource->delete();
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
        $fetched = $this->model->with($withRelationships)
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
        $fetched = $this->model->with($withRelationships)
            ->orderBy($orderByField, 'desc')
            ->take($count)
            ->get()->all();

        return $fetched;
    }

    public function getByIds(array $ids = [])
    {
        $query = $this->model->newQuery();
        $results = $query->findMany($ids)->all();

        return $results;
    }

    public function countAll()
    {
        $eloquentQuery = $this->model->newQuery();
        $queryBuilder = $eloquentQuery->getQuery();

        return $queryBuilder->count();
    }
}