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
use Angelov\Eestec\Platform\Commands\Documents\UpdateDocumentCommand;
use Angelov\Eestec\Platform\Events\Documents\DocumentWasOpened;
use Angelov\Eestec\Platform\Http\Requests\StoreDocumentRequest;
use Angelov\Eestec\Platform\Paginators\DocumentsPaginator;
use Angelov\Eestec\Platform\Repositories\DocumentsRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\TagsRepositoryInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
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
    protected $tags;
    protected $events;

    public function __construct(
        DocumentsRepositoryInterface $documents,
        TagsRepositoryInterface $tags,
        Factory $views,
        Dispatcher $commandBus,
        EventsDispatcher $events
    ) {
        $this->views = $views;
        $this->commandBus = $commandBus;
        $this->documents = $documents;
        $this->tags = $tags;
        $this->events = $events;
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
        $documents = $paginator->get($page, $with = ['submitter', 'openedBy', 'tags']);
        $tags = $this->tags->all($with = ['documents']);

        return $this->views->make('documents.index', compact('documents', 'tags'));
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
     * @param Guard $authenticator
     * @param int $id
     * @return RedirectResponse
     */
    public function show(Redirector $redirector, Guard $authenticator, $id)
    {
        $document = $this->documents->get($id);
        $member = $authenticator->user();

        $this->events->fire(new DocumentWasOpened($document, $member));

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

    public function byTag(TagsRepositoryInterface $tags, $id)
    {
        $tag = $tags->get($id);
        $documents = $tag->getDocuments();
        $tags = $tags->all($with = ['documents']);

        return $this->views->make('documents.by-tag', compact('tag', 'documents', 'tags'));
    }

    public function edit($id)
    {
        $document = $this->documents->get($id);

        return $this->views->make('documents.modals.edit-document', compact('document'));
    }

    public function update(StoreDocumentRequest $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;

        $this->commandBus->dispatch(new UpdateDocumentCommand($id, $data));

        return $this->successfulJsonResponse("Document updated successfully.", ['document' => $data]);
    }

    public function listTags(TagsRepositoryInterface $tags)
    {
        $all = $tags->all();
        return new JsonResponse($all);
    }
}

