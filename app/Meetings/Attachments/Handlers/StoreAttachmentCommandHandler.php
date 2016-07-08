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

namespace Angelov\Eestec\Platform\Meetings\Attachments\Handlers;

use Angelov\Eestec\Platform\Meetings\Attachments\Attachment;
use Angelov\Eestec\Platform\Meetings\Attachments\Commands\StoreAttachmentCommand;
use Angelov\Eestec\Platform\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;

class StoreAttachmentCommandHandler
{
    protected $members;
    protected $attachments;

    public function __construct(MembersRepositoryInterface $members, AttachmentsRepositoryInterface $attachments)
    {
        $this->members = $members;
        $this->attachments = $attachments;
    }

    public function handle(StoreAttachmentCommand $command)
    {
        $attachment = new Attachment();

        $owner = $this->members->get($command->getOwnerId());
        $attachment->setOwner($owner);

        $file = $command->getFile();
        $filename = $file->getClientOriginalName();
        $size = $this->convertSizeToKilobytes($file->getSize());

        $attachment->setFilename($filename);
        $attachment->setSize($size);

        // @todo refactor
        $filename = md5($file->getClientOriginalName()) . "_" . md5(rand(0, 10000)) . "." . $file->getClientOriginalExtension();
        $file->move(storage_path("meetings/attachments"), $filename);

        $this->attachments->store($attachment);

        return $attachment;
    }

    private function convertSizeToKilobytes($inBytes)
    {
        return $inBytes / 1000;
    }
}