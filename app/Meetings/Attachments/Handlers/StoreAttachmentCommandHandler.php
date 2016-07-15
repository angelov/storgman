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

use Angelov\Eestec\Platform\Core\FileSystem\FileSystemsRegistry;
use Angelov\Eestec\Platform\Meetings\Attachments\Attachment;
use Angelov\Eestec\Platform\Meetings\Attachments\AttachmentFile;
use Angelov\Eestec\Platform\Meetings\Attachments\Commands\StoreAttachmentCommand;
use Angelov\Eestec\Platform\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;

class StoreAttachmentCommandHandler
{
    protected $members;
    protected $attachments;
    protected $filesystem;

    public function __construct(
        MembersRepositoryInterface $members,
        AttachmentsRepositoryInterface $attachments,
        FileSystemsRegistry $filesystems)
    {
        $this->members = $members;
        $this->attachments = $attachments;
        $this->filesystem = $filesystems->get(AttachmentFile::class);
    }

    public function handle(StoreAttachmentCommand $command)
    {
        $attachment = new Attachment();

        $owner = $this->members->get($command->getOwnerId());
        $attachment->setOwner($owner);

        $file = $command->getFile();
        $filename = $file->getFilename();

        $size = $this->convertSizeToKilobytes(100000); // todo fix

        $attachment->setFilename($filename);
        $attachment->setSize($size);

        $file = $this->filesystem->store($file, true);

        $attachment->setStorageFilename($file->getFilename());

        $this->attachments->store($attachment);

        return $attachment;
    }

    private function convertSizeToKilobytes($inBytes)
    {
        return $inBytes / 1000;
    }
}