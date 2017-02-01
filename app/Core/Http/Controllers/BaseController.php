<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Core\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use ValidatesRequests;

    /**
     * @param string $status
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    protected function respondWithJson($status, $message, $data = [])
    {
        $responseData = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        return new JsonResponse($responseData);
    }

    public function successfulJsonResponse($message, $data = [])
    {
        return $this->respondWithJson('success', $message, $data);
    }

    public function errorJsonResponse($message, $data = [])
    {
        return $this->respondWithJson('error', $message, $data);
    }
}
