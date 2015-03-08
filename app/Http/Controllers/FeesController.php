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

use Angelov\Eestec\Platform\Commands\Fees\DeleteFeeCommand;
use Angelov\Eestec\Platform\Commands\Fees\StoreFeeCommand;
use Angelov\Eestec\Platform\Http\Requests\StoreFeeRequest;
use Angelov\Eestec\Platform\Paginators\FeesPaginator;
use Angelov\Eestec\Platform\Services\MembershipService;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Angelov\Eestec\Platform\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Illuminate\View\Factory;

class FeesController extends BaseController
{
    protected $request;
    protected $fees;
    protected $members;
    protected $paginator;
    protected $membership;
    protected $view;
    protected $commandBus;

    public function __construct(
        Request $request,
        Factory $view,
        FeesRepositoryInterface $fees,
        MembersRepositoryInterface $members,
        MembershipService $membership,
        FeesPaginator $paginator,
        Dispatcher $commandBus
    ) {
        $this->request = $request;
        $this->fees = $fees;
        $this->members = $members;
        $this->paginator = $paginator;
        $this->membership = $membership;
        $this->view = $view;
        $this->commandBus = $commandBus;
    }

    /**
     * Display the main page for managing the fees
     *
     * @return View
     */
    public function index()
    {
        $latest = $this->fees->latest(5, ['member'], 'id');
        $toExpire = $this->fees->getSoonToExpire(5);
        $fees = json_encode($this->membership->getExpectedAndPaidFeesPerMonthLastYear());

        return $this->view->make('fees.index', compact('latest', 'toExpire', 'fees'));
    }

    /**
     * List all paid fees
     *
     * @return View
     */
    public function archive()
    {
        $page = $this->request->get('page', 1);
        $fees = $this->paginator->get($page, ['member']);

        return $this->view->make('fees.archive', compact('fees'));
    }

    /**
     * Show the form for creating a new fee.
     * Method available only via ajax.
     *
     * @return View
     */
    public function create()
    {
        $memberId = $this->request->get('member_id');
        $member = $this->members->get($memberId);

        $suggestDates = $this->membership->suggestDates($member);

        return $this->view->make('members.modals.renew-membership', compact('member', 'suggestDates'))->render();
    }

    /**
     * Store a newly created fee.
     *
     * @param StoreFeeRequest $request
     * @return JsonResponse
     */
    public function store(StoreFeeRequest $request)
    {
        $memberId = $request->get('member_id');
        $from = $request->get('from');
        $to = $request->get('to');

        $this->commandBus->dispatch(new StoreFeeCommand($memberId, $from, $to));

        return $this->successfulJsonResponse('The membership was renewed successfully.');
    }

    /**
     * Remove the specified fee from storage.
     * Method available only via ajax.
     *
     * @param  int      $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->commandBus->dispatch(new DeleteFeeCommand($id));

        return $this->successfulJsonResponse('Fee deleted successfully.');
    }
}
