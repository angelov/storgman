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

namespace Angelov\Storgman\Members\Http\Requests;

use Angelov\Storgman\Core\Http\Request;

class StoreMemberRequest extends Request
{
    protected $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|unique:members|email',
        'password' => 'required|min:6',
        'faculty' => 'required',
        'field_of_study' => 'required',
        'year_of_graduation' => 'required|integer',
        'birthday' => 'required|date_format:Y-m-d',
        'member_photo' => 'image|max:3000',
        'website' => 'url'
    ];
}
