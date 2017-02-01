<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Faculties\Repositories;

use Angelov\Storgman\Core\Repositories\AbstractEloquentRepository;
use Angelov\Storgman\Faculties\Faculty;
use Angelov\Storgman\Faculties\Reports\MembersPerFacultyReport;
use Illuminate\Support\Facades\DB;

class EloquentFacultiesRepository extends AbstractEloquentRepository implements FacultiesRepositoryInterface
{
    public function __construct(Faculty $entity)
    {
        parent::__construct($entity);
    }

    public function store(Faculty $faculty)
    {
        $faculty->save();
    }

    public function getEnabled()
    {
        return Faculty::where('enabled', true)->get()->all();
    }

    public function countPerFaculty()
    {
        // The query works with both MySQL and PostgreSQL
        $results = (array)DB::select(
            '
                SELECT faculty_id,
                       count(id) AS members
                FROM members
                WHERE faculty_id IS NOT NULL
                AND members.approved = TRUE
                GROUP BY faculty_id
                ORDER BY members DESC;
            '
        );

        $resultsById = [];

        foreach ($results as $result) {
            $resultsById[$result->faculty_id] = (int) $result->members;
        }

        $ids = array_keys($resultsById);

        /** @var Faculty[] $faculties */
        $faculties = $this->getByIds($ids);

        $report = new MembersPerFacultyReport();

        foreach ($faculties as $faculty) {
            $count = $resultsById[$faculty->getId()];
            $report->addFaculty($faculty, $count);
        }

        return $report;
    }
}