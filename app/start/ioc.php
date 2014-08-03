<?php

/**
 * Binding interfaces to implementations
 */

App::bind('Angelov\Eestec\Platform\Repository\MembersRepositoryInterface',
          'Angelov\Eestec\Platform\Repository\EloquentMembersRepository');

App::bind('Angelov\Eestec\Platform\Repository\FeesRepositoryInterface',
          'Angelov\Eestec\Platform\Repository\EloquentFeesRepository');

App::bind('Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface',
          'Angelov\Eestec\Platform\Repository\LocalPhotosRepository');

App::bind('Angelov\Eestec\Platform\Repository\MeetingsRepositoryInterface',
          'Angelov\Eestec\Platform\Repository\EloquentMeetingsRepository');

App::bind('MembershipService', function () {
    return App::make('Angelov\Eestec\Platform\Service\MembershipService');
});

App::bind('MeetingsService', function () {
    return App::make('Angelov\Eestec\Platform\Service\MeetingsService');
});

App::bind('PhotosRepository', function () {
    return App::make('Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface');
});
