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

use Carbon\Carbon;
use Angelov\Eestec\Platform\Exception\MemberNotFoundException;
use Angelov\Eestec\Platform\Model\Member;
use DB;

class EloquentMembersRepository implements MembersRepositoryInterface {

    /**
     * Returns all members from the database
     */
    public function all() {
        return Member::all();
    }

    public function destroy($id) {

        if (null == Member::find($id)) {
            throw new MemberNotFoundException();
        }

        Member::destroy($id);
    }

    public function store(Member $member) {
        $member->save();
    }

    public function get($id) {
        $member = Member::find($id);

        if ($member == null) {
            throw new MemberNotFoundException();
        }

        return $member;
    }

    public function getByPage($page = 1, $limit = 20) {
        $results = new \stdClass();
        $results->page = $page;
        $results->limit = $limit;
        $results->totalItems = 0;
        $results->items = array();

        $members = Member::skip($limit * ($page - 1))->take($limit)->get();

        $results->totalItems = Member::count();
        $results->items = $members->all();

        return $results;
    }

    public function countByMembershipStatus() {

        $result = (array) DB::select('
            select *
            from
              (select count(id) as total
               from members)
               as tbl1,

              (select count(id) as active
               from members
               where id in
                  (select distinct member_id from fees)
               )
               as tbl2;
        ')[0];

        return $result;

    }

    public function getByBirthdayDate(\DateTime $date) {

        $members = Member::whereRaw('EXTRACT(DAY from birthday) = ? and EXTRACT(MONTH from birthday) = ?',
                                    [$date->format('d'), $date->format('m')])->get()->all();

        return $members;

    }

    public function getByIds(array $ids) {
        return Member::whereIn('id', $ids)->get()->all();
    }

}