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

namespace Angelov\Storgman\Documents\Tags;

use Angelov\Storgman\Documents\Document;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = "tags";

    protected $colors = [
        "#999",
        "#428bca",
        "#5cb85c",
        "#5bc0de",
        "#f0ad4e",
        "#d9534f"
    ];

    public function __construct()
    {
        $this->setRandomColor();
        parent::__construct();
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function getColor()
    {
        return $this->getAttribute('color');
    }

    public function setColor($color)
    {
        $this->setAttribute('color', $color);
    }

    public function setRandomColor()
    {
        $color = $this->colors[array_rand($this->colors)];
        $this->setColor($color);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class);
    }

    public function countDocuments()
    {
        return $this->documents->count();
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documents->all();
    }

    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getAttribute('updated_at');
    }
}
