<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Meetings\Attachments\Packaging;

use Angelov\Storgman\Meetings\Attachments\Attachment;
use SplFileInfo;
use ZipArchive;

class ZipAttachmentsPacker implements AttachmentsPackerInterface
{
    /**
     * @param Attachment[] $attachments
     * @param string $filename
     * @return SplFileInfo
     * @throws NoAttachmentsProvidedException
     */
    public function pack(array $attachments, $filename)
    {
        if (count($attachments) == 0) {
            throw new NoAttachmentsProvidedException();
        }

        $filename .= ".zip";

        $archive = new ZipArchive();
        $archive->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $path = storage_path("meetings/attachments/");

        foreach ($attachments as $attachment) {
            $archive->addFile($path . $attachment->getStorageFilename(), $attachment->getFilename());
        }

        $file = new SplFileInfo($filename);

        return $file;
    }

    public function getSupportedFormats()
    {
        return ["zip"];
    }
}