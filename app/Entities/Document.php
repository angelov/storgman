<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Entities;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = "documents";

    protected $tagList = [];
    protected $openedByList = [];

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
    }

    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    public function setDescription($description)
    {
        $this->setAttribute('description', $description);
    }

    public function getUrl()
    {
        return $this->getAttribute('url');
    }

    public function setUrl($url)
    {
        $this->setAttribute('url', $url);
    }

    public function submitter()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Entities\Member', 'submitted_by');
    }

    /**
     * @return Member
     */
    public function getSubmitter()
    {
        return $this->submitter;
    }

    /**
     * @param Member $member
     */
    public function setSubmitter(Member $member)
    {
        $this->submitter()->associate($member);
    }

    public function openedBy()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entities\Member', 'document_openings')->withTimestamps();
    }

    public function addOpener(Member $member)
    {
        $this->openedByList[] = $member;
    }

    /**
     * @return Member[]
     */
    public function getOpeners()
    {
        return $this->openedBy->all();
    }

    public function countOpenings()
    {
        return $this->openedBy->count();
    }

    public function countOpeners()
    {
        $openers = $this->getOpeners();
        $counted = [];

        foreach ($openers as $opener) {
            if (! in_array($opener->getId(), $counted)) {
                $counted[] = $opener->getId();
            }
        }

        return count($counted);
    }

    public function tags()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entities\Tag');
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $this->tagList[] = $tag;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute('updated_at');
    }

    /**
     * By default, all members have access to the document
     */
    public function setVisibleToAllMembers()
    {
        $this->setAttribute('board_only', false);
    }

    /**
     * @return bool
     */
    public function isVisibleToAllMembers()
    {
        return (! $this->getAttribute('board_only'));
    }

    public function setVisibleToBoardOnly()
    {
        $this->setAttribute('board_only', true);
    }

    public function save(array $options = [])
    {
        parent::save($options);

        foreach ($this->tagList as $tag) {
            $this->tags()->attach($tag);
        }

        foreach ($this->openedByList as $opener) {
            $this->openedBy()->attach($opener);
        }
    }
}
