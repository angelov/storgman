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

namespace Angelov\Eestec\Platform\Validation;

use Illuminate\Validation\Factory;

abstract class Validator {

    protected $validatorFactory;
    protected $messages = [];
    protected $rules;

    public function __construct(Factory $validatorFactory) {
        $this->validatorFactory = $validatorFactory;
    }

    public function validate(array $data) {
        $validator = $this->validatorFactory->make($data, $this->rules);

        if ($validator->fails()) {
            $this->messages = $validator->messages()->all();
            return false;
        }

        return true;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function getRules() {
        return $this->rules;
    }

    public function removeRule($field, $rule) {
        $pattern = "/". $rule .":[a-zA-Z,\-0-9]*/";
        $existing = $this->rules[$field];

        $this->rules[$field] = preg_replace($pattern, "", $existing);
    }

    public function addRule($field, $rule) {
        $this->rules[$field] = $this->rules[$field] . "|" . $rule;
    }

}