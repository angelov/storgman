<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Settings\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Faculties\Commands\ChangeFacultyStatusCommand;
use Angelov\Storgman\Faculties\Commands\DeleteFacultyCommand;
use Angelov\Storgman\Faculties\Commands\StoreFacultyCommand;
use Angelov\Storgman\Faculties\Commands\UpdateFacultyCommand;
use Angelov\Storgman\Faculties\Exceptions\FacultyHasMembersException;
use Angelov\Storgman\Faculties\Repositories\FacultiesRepositoryInterface;
use Angelov\Storgman\Settings\Http\Requests\StoreFacultyRequest;
use Illuminate\Http\JsonResponse;

class FacultiesController extends BaseController
{
    protected $faculties;

    public function __construct(FacultiesRepositoryInterface $faculties)
    {
        $this->faculties = $faculties;
    }

    public function index()
    {
        $faculties = $this->faculties->all();
        $perFaculty = json_encode($this->faculties->countPerFaculty());

        return view('settings.faculties.index', compact('faculties', 'perFaculty'));
    }

    /**
     * Add a faculty to the list of supported faculties
     * Method available only via AJAX requests
     *
     * @param StoreFacultyRequest $request
     * @return JsonResponse
     */
    public function store(StoreFacultyRequest $request)
    {
        $title = $request->get('title');
        $abbreviation = $request->get('abbreviation');
        $university = $request->get('university');

        $faculty = dispatch(new StoreFacultyCommand($title, $abbreviation, $university));

        $data['view'] = view('settings.faculties.partials.faculty-row', compact('faculty'))->render();

        return $this->successfulJsonResponse("Faculty successfully added to the list.", $data);
    }

    public function enable($id)
    {
        dispatch(new ChangeFacultyStatusCommand($id, ChangeFacultyStatusCommand::STATUS_ENABLED));

        $data['enabled'] = ChangeFacultyStatusCommand::STATUS_ENABLED;

        return $this->successfulJsonResponse("Faculty successfully enabled.", $data);
    }

    public function disable($id)
    {
        dispatch(new ChangeFacultyStatusCommand($id, ChangeFacultyStatusCommand::STATUS_DISABLED));

        $data['enabled'] = ChangeFacultyStatusCommand::STATUS_DISABLED;

        return $this->successfulJsonResponse("Faculty successfully disabled.", $data);

    }

    public function delete($id)
    {
        try {
            dispatch(new DeleteFacultyCommand($id));
        } catch (FacultyHasMembersException $e) {
            return $this->errorJsonResponse("There are members who have chosen the faculty you wanted to delete. Please disable it instead.");
        }

        return $this->successfulJsonResponse("Faculty successfully deleted");
    }

    public function edit($id)
    {
        $faculty = $this->faculties->get($id);

        return view('settings.faculties.modals.modal-edit-faculty', compact('faculty'))->render();
    }

    public function update($id, StoreFacultyRequest $request)
    {
        $title = $request->get('title');
        $abbreviation = $request->get('abbreviation');
        $university = $request->get('university');

        dispatch(new UpdateFacultyCommand($id, $title, $abbreviation, $university));

        return $this->successfulJsonResponse("Faculty successfully updated.");
    }
}
