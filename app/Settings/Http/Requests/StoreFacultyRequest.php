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

namespace Angelov\Storgman\Settings\Http\Requests;

use Angelov\Storgman\Core\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreFacultyRequest extends Request
{
    protected $rules = [
        'title'        => 'required',
        'abbreviation' => 'required',
        'university'   => 'required'
    ];

    public function response(array $errors)
    {
        $responseData = [
            'status' => "error",
            'message' => "Please fix the following errors:",
            'data' => [
                'errors' => $this->parseErrors($errors)
            ]
        ];

        return new JsonResponse($responseData);
    }
}