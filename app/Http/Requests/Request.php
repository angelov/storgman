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

namespace Angelov\Eestec\Platform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

abstract class Request extends FormRequest
{
    protected $session;
    protected $redirector;
    protected $rules = [];

    public function rules()
    {
        return $this->rules;
    }

    public function __construct(Store $session, Redirector $redirector)
    {
        $this->session = $session;
        $this->redirector = $redirector;
    }

    public function authorize()
    {
        return true;
    }

    public function response(array $errors)
    {
        $messages = $this->parseErrors($errors);

        $this->session->flash('errorMessages', $messages);
        return $this->redirector->back()->withInput();
    }

    /**
     * @param array $errors
     * @return array
     */
    protected function parseErrors(array $errors)
    {
        $messages = [];

        if (count($errors) > 0) {
            foreach ($errors as $field => $msgs) {
                $messages = array_merge($messages, $msgs);
            }
        }

        return $messages;
    }

    public function removeRule($field, $rule)
    {
        $pattern = "/" . $rule . "[:[a-zA-Z,\-0-9]*]*/";
        $existing = $this->rules[$field];

        $this->rules[$field] = preg_replace($pattern, "", $existing);
    }

    public function addRule($field, $rule)
    {
        $this->rules[$field] = $this->rules[$field] . "|" . $rule;
    }
}
