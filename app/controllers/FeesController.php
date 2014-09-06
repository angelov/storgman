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

use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Angelov\Eestec\Platform\Service\MembershipService;
use Angelov\Eestec\Platform\Validation\FeesValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Model\Fee;
use Angelov\Eestec\Platform\Repository\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

class FeesController extends \BaseController
{

    protected $request;
    protected $fees;
    protected $members;
    protected $validator;

    public function __construct(
        Request $request,
        FeesRepositoryInterface $fees,
        MembersRepositoryInterface $members,
        FeesValidator $validator
    ) {
        $this->request = $request;
        $this->fees = $fees;
        $this->members = $members;
        $this->validator = $validator;
    }

    /**
     * Show the form for creating a new fee.
     * Method available only via ajax.
     *
     * @return Response
     */
    public function create()
    {
        $member_id = $this->request->get('member_id');
        $member = $this->members->get($member_id);

        /** @var MembershipService $membershipService */
        $membershipService = App::make('MembershipService');

        $exp = $membershipService->getExpirationDate($member);

        $member->membership_status = $membershipService->isMemberActive($member);
        $member->membership_expiration_date = $exp;

        $suggestDates = [];

        if ($exp != null) {

            $exp = clone $exp;
            $suggestDates['from'] = $exp->modify('+1 day')->format('Y-m-d');
            $suggestDates['to'] = $exp->modify('+1 year')->format('Y-m-d');

        } else {

            $today = new \DateTime('now');
            $suggestDates['from'] = $today->format('Y-m-d');
            $suggestDates['to'] = $today->modify('+1 year')->format('Y-m-d');

        }

        $fees = $this->fees->getFeesForMember($member);

        $response = new Response();
        $data = View::make('members.modals.renew-membership', compact('member', 'suggestDates', 'fees'))->render();
        $response->setContent($data);

        return $response;

    }

    /**
     * Store a newly created fee.
     *
     * @return Response
     */
    public function store()
    {
        $data = [];

        if (!$this->validator->validate($this->request->all())) {
            $data['status'] = 'danger';
            $data['message'] = 'The data you entered is invalid.';

            return new JsonResponse($data);
        }

        $fee = new Fee();

        $fee->from_date = $this->request->get('from');
        $fee->to_date = $this->request->get('to');

        $member = $this->members->get($this->request->get('member_id'));

        $this->fees->store($fee, $member);

        $data['status'] = 'success';
        $data['message'] = 'The membership was renewed successfully.';

        return new JsonResponse($data);

    }

    /**
     * Remove the specified fee from storage.
     * Method available only via ajax.
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = [];

        try {
            $this->fees->destroy($id);

            $data['status'] = 'success';
            $data['message'] = 'Fee deleted successfully.';
        } catch (ResourceNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);

    }

}
