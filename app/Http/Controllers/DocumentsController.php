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

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\Commands\Documents\StoreDocumentCommand;
use Angelov\Eestec\Platform\Http\Requests\StoreDocumentRequest;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DocumentsController extends BaseController
{
    protected $views;
    protected $commandBus;

    public function __construct(Factory $views, Dispatcher $commandBus)
    {
        $this->views = $views;
        $this->commandBus = $commandBus;
    }

    /**
     * List the submitted documents
     *
     * @return View
     */
    public function index()
    {
        return $this->views->make('documents.index');
    }

    /**
     * Store a new document
     *
     * @param StoreDocumentRequest $request
     * @return View
     */
    public function store(StoreDocumentRequest $request)
    {
        $data = $request->all();
        $document = $this->commandBus->dispatch(new StoreDocumentCommand($data));

        return $this->views->make('documents.components.document', compact('document'));
    }
}
 