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

class UpdateMemberRequest extends StoreMemberRequest
{
    public function validate()
    {
        /**
         * If the unique email rule is set and the member's email
         * is not changed, the system will consider the email as
         * already taken and will throw an error.
         */
//        if ($req == $this->request->get('email')) {
//            $this->validator->removeRule('email', 'unique');
//        }
        $this->removeRule('email', 'unique'); // @todo TEMPORARY

        /**
         * We don't want to change the member's password if there's
         * no new password inserted.
         */
        if ($this->get('password') == '') {
            $this->removePasswordRules();
        }

        parent::validate();
    }

    protected function removePasswordRules()
    {
        $this->removeRule('password', 'required');
        $this->removeRule('password', 'min');
    }
}
 