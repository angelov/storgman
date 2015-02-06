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

namespace Angelov\Eestec\Platform\Repositories;

use Angelov\Eestec\Platform\Exceptions\ResourceNotFoundException;
use Angelov\Eestec\Platform\Entities\Fee;
use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Entities\Member;

interface RepositoryInterface
{
    /**
     * Returns all items
     *
     * @return array
     */
    public function all();

    /**
     * Returns array of items for a specific page
     *
     * @param int $page
     * @param int $limit
     * @param array $withRelationships
     * @return \stdClass
     */
    public function getByPage($page, $limit, array $withRelationships);

    /**
     * Returns the latest N items
     *
     * @param $count
     * @param array $withRelationships
     * @return array
     */
    public function latest($count, array $withRelationships = []);

    /**
     * Deletes a fee from the storage
     *
     * @param $id int
     * @return void
     */
    public function destroy($id);

    /**
     * Returns the member with the given ID
     *
     * @param int $id
     * @return Fee|Meeting|Member
     * @throws ResourceNotFoundException
     */
    public function get($id);

    /**
     * Returns the member with the specific IDs
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids);

    /**
     * Counts the members
     *
     * @return int
     */
    public function countAll();
}