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

namespace Angelov\Storgman\Membership\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Membership\Commands\DeleteFeeCommand;
use Angelov\Storgman\Membership\Commands\StoreFeeCommand;
use Angelov\Storgman\Membership\Http\Requests\StoreFeeRequest;
use Angelov\Storgman\Membership\FeesPaginator;
use Angelov\Storgman\Membership\MembershipService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class FeesController extends BaseController
{
    protected $fees;
    protected $members;
    protected $paginator;
    protected $membership;

    public function __construct(FeesRepositoryInterface $fees, MembersRepositoryInterface $members, MembershipService $membership)
    {
        $this->fees = $fees;
        $this->members = $members;
        $this->membership = $membership;
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

        return view('fees.index', compact('latest', 'toExpire', 'fees'));
    }

    /**
     * List all paid fees
     *
     * @param Request $request
     * @param FeesPaginator $paginator
     * @return View
     */
    public function archive(Request $request, FeesPaginator $paginator)
    {
        $page = $request->get('page', 1);
        $fees = $paginator->get($page, ['member']);

        return view('fees.archive', compact('fees'));
    }

    /**
     * Show the form for creating a new fee.
     * Method available only via ajax.
     *
     * @param Request $request
     * @return string The rendered view
     */
    public function create(Request $request)
    {
        $memberId = $request->get('member_id');
        $member = $this->members->get($memberId);

        $suggestDates = $this->membership->suggestDates($member);

        return view('members.modals.renew-membership', compact('member', 'suggestDates'))->render();
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

        dispatch(new StoreFeeCommand($memberId, $from, $to));

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
        dispatch(new DeleteFeeCommand($id));

        return $this->successfulJsonResponse('Fee deleted successfully.');
    }
}
