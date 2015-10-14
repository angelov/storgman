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

namespace Angelov\Eestec\Platform\Membership\Http\Requests;

use Angelov\Eestec\Platform\Core\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreFeeRequest extends Request
{
    /** @todo The date in the "to" field must be after the date in the "from" field */
    protected $rules = [
        'from' => 'required|date_format:Y-m-d',
        'to' => 'required|date_format:Y-m-d',
        'member_id' => 'required|exists:members,id'
    ];

    public function response(array $errors)
    {
        $data['status'] = 'danger';
        $data['message'] = 'The data you entered is invalid.';

        return new JsonResponse($data);
    }
}