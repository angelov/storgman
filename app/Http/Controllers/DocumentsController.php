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

use Angelov\Eestec\Platform\Commands\Documents\DeleteDocumentCommand;
use Angelov\Eestec\Platform\Commands\Documents\StoreDocumentCommand;
use Angelov\Eestec\Platform\Http\Requests\StoreDocumentRequest;
use Angelov\Eestec\Platform\Paginators\DocumentsPaginator;
use Angelov\Eestec\Platform\Repositories\DocumentsRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class DocumentsController extends BaseController
{
    protected $views;
    protected $commandBus;
    protected $documents;

    public function __construct(DocumentsRepositoryInterface $documents, Factory $views, Dispatcher $commandBus)
    {
        $this->views = $views;
        $this->commandBus = $commandBus;
        $this->documents = $documents;
    }

    /**
     * List the submitted documents
     *
     * @param Request $request
     * @param DocumentsPaginator $paginator
     * @return View
     */
    public function index(Request $request, DocumentsPaginator $paginator)
    {
        $page = $request->get('page', 1);
        $documents = $paginator->get($page, $with = ['submitter']);

        return $this->views->make('documents.index', compact('documents'));
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

    /**
     * Redirect the user to the document's url
     *
     * @param Redirector $redirector
     * @param int $id
     * @return RedirectResponse
     */
    public function show(Redirector $redirector, $id)
    {
        // @todo Fire an "DocumentWasOpened" event

        $document = $this->documents->get($id);

        return $redirector->to($document->getUrl(), 301);
    }

    /**
     * Delete the specific document
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        // @todo Check if the member is allowed to delete the document

        $this->commandBus->dispatch(new DeleteDocumentCommand($id));

        return $this->successfulJsonResponse("Document deleted successfully.");
    }
}
