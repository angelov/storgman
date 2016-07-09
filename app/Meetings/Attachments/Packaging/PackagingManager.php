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

namespace Angelov\Eestec\Platform\Meetings\Attachments\Packaging;

use SplFileInfo;

final class PackagingManager
{
    /** @var AttachmentsPackerInterface[] */
    protected $packers = [];

    public function addPacker(AttachmentsPackerInterface $packer)
    {
        foreach ($packer->getSupportedFormats() as $format) {
            $this->packers[$format] = $packer;
        }

        return $this;
    }

    /**
     * @param array $attachments
     * @param string $format
     * @param string $filename
     * @return SplFileInfo
     * @throws FormatNotSupportedException
     */
    public function pack(array $attachments, $format = "zip", $filename = "")
    {
        if (! isset($this->packers[$format])) {
            throw new FormatNotSupportedException(sprintf(
                "The requested package format [%s] is not supported.",
                $format
            ));
        }

        $filename = ($filename != "") ? $filename : $this->generateFileName();

        return $this->packers[$format]->pack($attachments, $filename);
    }

    public function getPackers()
    {
        return $this->packers;
    }

    private function generateFileName()
    {
        return md5((new \DateTime())->getTimestamp() . rand(0, 1000));
    }
}
