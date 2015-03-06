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

namespace Angelov\Eestec\Platform\Handlers\Commands\Members;

use Angelov\Eestec\Platform\Commands\Members\DeleteMemberCommand;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface;

class DeleteMemberCommandHandler
{
    protected $members;
    protected $photos;

    public function __construct(MembersRepositoryInterface $members, PhotosRepositoryInterface $photos)
    {
        $this->members = $members;
        $this->photos = $photos;
    }

    public function handle(DeleteMemberCommand $command)
    {
        $id = $command->getMemberId();

        $member = $this->members->get($id);
        $photo = $member->getPhoto();

        if (isset($photo)) {
            $this->photos->destroy($photo, 'members');
        }

        $this->members->destroy($id);
    }
}
