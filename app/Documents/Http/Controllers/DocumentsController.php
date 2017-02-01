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

namespace Angelov\Storgman\Documents\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Documents\Commands\DeleteDocumentCommand;
use Angelov\Storgman\Documents\Commands\StoreDocumentCommand;
use Angelov\Storgman\Documents\Commands\UpdateDocumentCommand;
use Angelov\Storgman\Documents\Events\DocumentWasOpened;
use Angelov\Storgman\Documents\Http\Requests\StoreDocumentRequest;
use Angelov\Storgman\Documents\DocumentsPaginator;
use Angelov\Storgman\Documents\Repositories\DocumentsRepositoryInterface;
use Angelov\Storgman\Documents\Tags\Repositories\TagsRepositoryInterface;
use Angelov\Storgman\Members\Member;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentsController extends BaseController
{
    protected $documents;
    protected $tags;
    protected $events;

    public function __construct(
        DocumentsRepositoryInterface $documents,
        TagsRepositoryInterface $tags,
        EventsDispatcher $events
    ) {
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

        return view('documents.index', compact('documents', 'tags'));
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
        $document = dispatch(new StoreDocumentCommand($data));

        return view('documents.components.document', compact('document'));
    }

    /**
     * Redirect the user to the document's url
     *
     * @param Guard $authenticator
     * @param int $id
     * @return RedirectResponse
     */
    public function show(Guard $authenticator, $id)
    {
        $document = $this->documents->get($id);

        /** @var Member $member */
        $member = $authenticator->user();

        $this->events->fire(new DocumentWasOpened($document, $member));

        return redirect()->to($document->getUrl(), 301);
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

        dispatch(new DeleteDocumentCommand($id));

        return $this->successfulJsonResponse("Document deleted successfully.");
    }

    public function byTag(TagsRepositoryInterface $tags, $id)
    {
        $tag = $tags->get($id);
        $documents = $tag->getDocuments();
        $tags = $tags->all($with = ['documents']);

        return view('documents.by-tag', compact('tag', 'documents', 'tags'));
    }

    public function edit($id)
    {
        $document = $this->documents->get($id);

        return view('documents.modals.edit-document', compact('document'));
    }

    public function update(StoreDocumentRequest $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;

        dispatch(new UpdateDocumentCommand($id, $data));

        return $this->successfulJsonResponse("Document updated successfully.", ['document' => $data]);
    }

    public function listTags(TagsRepositoryInterface $tags)
    {
        $all = $tags->all();
        return new JsonResponse($all);
    }
}
