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

namespace Angelov\Storgman\Core\FileSystem;

use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;

// @todo can be improved
class LocalFileSystem implements FileSystemInterface
{
    protected $filesystem;
    protected $basePath;

    public function __construct($basePath = "/")
    {
        $this->basePath = $basePath;
        $this->filesystem = new Filesystem(new Local($basePath));
    }

    public function store(File $file, $preserveFilename = false)
    {
        $filename = $this->generateFilename($file, $preserveFilename);
        $content = file_get_contents($file->getPath());

        $this->filesystem->write($filename, $content);

        $path = $this->basePath . DIRECTORY_SEPARATOR . $filename;

        return new File($filename, $path);
    }

    protected function generateFilename(File $file, $preserveOriginal = false)
    {
        if ($preserveOriginal) {
            $prefix = 1;
            $filename = $file->getFilename();
            while (true) {
                if (! $this->filesystem->has($filename)) {
                    return $filename;
                }
                $filename = $this->addFilenamePrefix($filename, $prefix++);
            }
        }

        return md5($file->getFilename()) . "_" . md5(rand(0, 100000)) . "." . $file->getExtension();
    }

    protected function addFilenamePrefix($filename, $prefix)
    {
        $parts = explode(".", $filename);
        $filename = array_slice($parts, 0, count($parts) - 1);
        $extension = $parts[count($parts) - 1];
        $filename = join(".", $filename);
        $filename .= sprintf(" (%s).%s", $prefix, $extension);

        return $filename;
    }

    public function find($filename)
    {
        if (! $this->filesystem->has($filename)) {
            throw new FileNotFoundException(sprintf(
                "Could not find file %s",
                $filename
            ));
        }

        $path = $this->basePath . DIRECTORY_SEPARATOR . $filename;

        return new File($filename, $path);

    }

    public function read(File $file)
    {
        return $this->filesystem->read($file->getFilename());
    }

    public function setBasePath($path)
    {
        $this->basePath = $path;
        $this->filesystem = new Filesystem(new Local($path));
    }
}