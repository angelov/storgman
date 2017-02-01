<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Members\Handlers;

use Angelov\Storgman\Members\Commands\UpdateMemberCommand;
use Angelov\Storgman\Members\MembersPopulator;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Angelov\Storgman\Members\Photos\Repositories\PhotosRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateMemberCommandHandler
{
    protected $members;
    protected $photos;
    protected $populator;

    public function __construct(
        MembersRepositoryInterface $members,
        PhotosRepositoryInterface $photos,
        MembersPopulator $populator
    ) {
        $this->members = $members;
        $this->photos = $photos;
        $this->populator = $populator;
    }

    public function handle(UpdateMemberCommand $command)
    {
        $member = $this->members->get($command->getMemberId());
        $data = $command->getMemberData();

        $this->populator->populateFromArray($member, $data);

        if (isset($data['member_photo'])) {
            /** @var UploadedFile $photo */
            $photo = $data['member_photo'];
            $photoFileName = md5($member->getEmail()) . "." . $photo->getClientOriginalExtension();

            $this->photos->store($photo, 'members', $photoFileName);

            $member->setPhoto($photoFileName);
        }

        $this->members->store($member);
    }
}
