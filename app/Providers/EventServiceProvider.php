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

namespace Angelov\Eestec\Platform\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        // Members Handlers

        'Angelov\Eestec\Platform\Events\Members\MemberWasApprovedEvent' => [
            'Angelov\Eestec\Platform\Handlers\Events\Members\EmailApprovalConfirmation'
        ],

        'Angelov\Eestec\Platform\Events\Members\MemberWasDeclinedEvent' => [
            'Angelov\Eestec\Platform\Handlers\Events\Members\EmailDenialConfirmation'
        ],

        'Angelov\Eestec\Platform\Events\Members\MemberJoinedEvent' => [
            'Angelov\Eestec\Platform\Handlers\Events\Members\EmailWelcomeMessage'
        ],

        // Membership Fees Handlers

        'Angelov\Eestec\Platform\Events\Fees\FeeWasProceededEvent' => [
            'Angelov\Eestec\Platform\Handlers\Events\Fees\EmailProceedingConfirmation'
        ],

        // Documents Handlers

        'Angelov\Eestec\Platform\Events\Documents\DocumentWasOpened' => [
            'Angelov\Eestec\Platform\Handlers\Events\Documents\TrackDocumentOpening'
        ]

    ];

}
