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

namespace Angelov\Storgman\Meetings\Attachments\Tasks;

use Angelov\Storgman\Meetings\Attachments\Commands\DeleteAttachmentCommand;
use Angelov\Storgman\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;

class CheckForUnusedAttachmentsTask
{
    protected $attachments;
    protected $commandBus;

    public function __construct(AttachmentsRepositoryInterface $attachments, Dispatcher $commandBus)
    {
        $this->attachments = $attachments;
        $this->commandBus = $commandBus;
    }

    public function execute()
    {
        $unused = $this->attachments->getUnused();

        foreach ($unused as $attachment) {

            $date = (new \DateTime())->modify('-12 hours');

            if ($attachment->getCreatedAt() <= $date) {
                $this->commandBus->dispatch(new DeleteAttachmentCommand($attachment->getId()));
            }

        }
    }
}