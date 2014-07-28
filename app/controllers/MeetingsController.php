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

use Illuminate\Http\Request;
use Angelov\Eestec\Platform\Model\Meeting;
use Angelov\Eestec\Platform\Repository\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

class MeetingsController extends \BaseController
{

    protected $request;
    protected $meetings;
    protected $members;

    public function __construct(
        Request $request,
        MeetingsRepositoryInterface $meetings,
        MembersRepositoryInterface $members
    ) {
        $this->request = $request;
        $this->meetings = $meetings;
        $this->members = $members;

        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the meetings.
     * GET /meetings
     *
     * @return Response
     */
    public function index()
    {
        $meetings = $this->meetings->all($with = ['attendants']);

        return View::make('meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting report.
     * GET /meetings/create
     *
     * @return Response
     */
    public function create()
    {
        return View::make('meetings.create');
    }

    /**
     * Store a newly created meeting report in storage.
     * POST /meetings
     *
     * @return Response
     */
    public function store()
    {

        /** @todo Validate the input */

        $meeting = new Meeting();
        $meeting->date = $this->request->get('date');
        $meeting->location = $this->request->get('location');
        $meeting->info = $this->request->get('details');

        $ids = $this->request->get('attendants');
        $attendants = [];

        if ($ids != '|') {
            $ids = explode("|", $this->request->get('attendants'));
            $ids = array_filter(
                $ids,
                function ($value) {
                    return $value != '';
                }
            );

            $attendants = $this->members->getByIds($ids);
        }

        $creator = Auth::user();

        $this->meetings->store($meeting, $creator, $attendants);

        return Redirect::route('meetings.index');

    }

    /**
     * Display the specified resource.
     * GET /meetings/{id}
     *
     * @param  int      $id
     * @return Response
     */
    public function show($id)
    {
        $meeting = $this->meetings->get($id);

        return View::make('meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     * GET /meetings/{id}/edit
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /meetings/{id}
     *
     * @param  int      $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /meetings/{id}
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
