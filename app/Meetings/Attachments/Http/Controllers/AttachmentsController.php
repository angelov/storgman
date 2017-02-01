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

namespace Angelov\Storgman\Meetings\Attachments\Http\Controllers;

use Angelov\Storgman\Core\FileSystem\FileSystemsRegistry;
use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Meetings\Attachments\Attachment;
use Angelov\Storgman\Meetings\Attachments\AttachmentFile;
use Angelov\Storgman\Meetings\Attachments\Commands\StoreAttachmentCommand;
use Angelov\Storgman\Meetings\Attachments\Http\Requests\StoreAttachmentRequest;
use Angelov\Storgman\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Storgman\Members\Member;
use Illuminate\Contracts\Auth\Guard;

class AttachmentsController extends BaseController
{
    protected $attachments;

    public function __construct(AttachmentsRepositoryInterface $attachments)
    {
        $this->attachments = $attachments;
    }

    public function store(StoreAttachmentRequest $request, Guard $auth)
    {
        $file = $request->file('file');

        /** @var Member $owner */
        $owner = $auth->user();
        $owner = $owner->getId();

        $file = new AttachmentFile($file->getClientOriginalName(), $file->getRealPath());

        /** @var Attachment $attachment */
        $attachment = dispatch(new StoreAttachmentCommand($file, $owner));

        return $attachment->getId();
    }

    public function show($id, FileSystemsRegistry $registry)
    {
        $attachment = $this->attachments->get($id);
        $filesystem = $registry->get(AttachmentFile::class);

        $file = $filesystem->find($attachment->getStorageFilename());
        $content = $filesystem->read($file);

        return response($content)->header('Content-Disposition', sprintf('attachment;filename="%s"', $attachment->getFilename()));
    }
}
