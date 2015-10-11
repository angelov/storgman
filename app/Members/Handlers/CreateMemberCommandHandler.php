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

namespace Angelov\Eestec\Platform\Members\Handlers;

use Angelov\Eestec\Platform\Members\Commands\CreateMemberCommand;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Members\Events\MemberJoinedEvent;
use Angelov\Eestec\Platform\Members\MembersPopulator;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Members\Photos\Repositories\PhotosRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateMemberCommandHandler
{
    protected $members;
    protected $populator;
    protected $events;
    protected $photos;

    public function __construct(
        MembersRepositoryInterface $members,
        PhotosRepositoryInterface $photos,
        MembersPopulator $populator,
        Dispatcher $events)
    {
        $this->members = $members;
        $this->photos = $photos;
        $this->populator = $populator;
        $this->events = $events;
    }

    public function handle(\Angelov\Eestec\Platform\Members\Commands\CreateMemberCommand $command)
    {
        $member = new Member();
        $data = $command->getMemberData();

        $this->populator->populateFromArray($member, $data);

        if (isset($data['member_photo'])) {

            /** @var UploadedFile $photo */
            $photo = $data['member_photo'];
            $photoFileName = md5($member->getEmail()) . "." . $photo->getClientOriginalExtension();

            $this->photos->store($photo, 'members', $photoFileName);

            $member->setPhoto($photoFileName);
        }

        if ($command->shouldBeApproved()) { // Member was added by a board member

            $member->setApproved(true);
        } else { // The member created his account

            $this->events->fire(new MemberJoinedEvent($member));
        }

        $this->members->store($member);
    }
}
