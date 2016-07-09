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

namespace Angelov\Eestec\Platform\Meetings\Attachments\Http\Controllers;

use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Angelov\Eestec\Platform\Meetings\Attachments\Attachment;
use Angelov\Eestec\Platform\Meetings\Attachments\Commands\StoreAttachmentCommand;
use Angelov\Eestec\Platform\Meetings\Attachments\Http\Requests\StoreAttachmentRequest;
use Angelov\Eestec\Platform\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Member;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Bus\Dispatcher;

class AttachmentsController extends BaseController
{
    protected $commandBus;
    protected $attachments;

    public function __construct(AttachmentsRepositoryInterface $attachments, Dispatcher $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->attachments = $attachments;
    }

    public function store(StoreAttachmentRequest $request, Guard $auth)
    {
        $file = $request->file('file');

        /** @var Member $owner */
        $owner = $auth->user();
        $owner = $owner->getId();

        /** @var Attachment $attachment */
        $attachment = $this->commandBus->dispatch(new StoreAttachmentCommand($file, $owner));

        return $attachment->getId();
    }

    public function show($id)
    {
        $attachment = $this->attachments->get($id);
        $path = storage_path('meetings/attachments/') . $attachment->getStorageFilename();

        return response()->download($path, $attachment->getFilename());
    }
}