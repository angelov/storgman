<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Repository;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class LocalPhotosRepository implements PhotosRepositoryInterface {

    /**
     * @inheritdoc
     */
    public function store(File $photo, $type, $fileName = null) {
        $fullPath = public_path() ."/" . Config::get('main.photos.upload_dir') . "/" . $type;
        $fileName = (isset($fileName)) ? $fileName : $photo->getClientOriginalName();

        $photo->move($fullPath, $fileName);
    }

    public function destroy($filename, $type) {
        $fullPath = Config::get('main.photos.upload_dir') . "/" . $type;

        unlink($fullPath . "/" . $filename);
    }

}