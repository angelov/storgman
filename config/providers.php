<?php

/**
 * Service providers
 */

return [

    /*
     * Laravel Framework Service Providers...
     */

    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,

    /*
     * Other vendor providers
     */

    TwigBridge\ServiceProvider::class,
    Intervention\Image\ImageServiceProviderLaravel5::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    Collective\Bus\BusServiceProvider::class,

    /*
     * Application Service Providers
     */

    // Core

    Angelov\Eestec\Platform\Core\Providers\AppServiceProvider::class,
    Angelov\Eestec\Platform\Core\Providers\BusServiceProvider::class,
    Angelov\Eestec\Platform\Core\Providers\ConfigServiceProvider::class,
    Angelov\Eestec\Platform\Core\Providers\RouteServiceProvider::class,
    Angelov\Eestec\Platform\Core\FileSystem\FileSystemsRegistryServiceProvider::class,
    Angelov\Eestec\Platform\Core\Doctrine\DoctrineServiceProvider::class,

    // Members

    Angelov\Eestec\Platform\Members\Providers\MembersRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Members\Photos\Providers\PhotosRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Members\Providers\MembersPopulatorServiceProvider::class,
    Angelov\Eestec\Platform\Members\Providers\EventsServiceProvider::class,
    Angelov\Eestec\Platform\Members\Providers\ViewComposersServiceProvider::class,

    // Social Profiles

    Angelov\Eestec\Platform\Members\SocialProfiles\Providers\SocialProfilesRepositoryServiceProvider::class,

    // Membership (Fees)

    Angelov\Eestec\Platform\Membership\Providers\FeesRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Membership\Providers\EventsServiceProvider::class,

    // Meetings

    Angelov\Eestec\Platform\Meetings\Providers\MeetingsRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Meetings\Providers\EventsServiceProvider::class,
    Angelov\Eestec\Platform\Meetings\Attachments\Providers\AttachmentsRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Meetings\Attachments\Providers\EventsServiceProvider::class,
    Angelov\Eestec\Platform\Meetings\Attachments\Packaging\PackagingManagerServiceProvider::class,

    // Documents

    Angelov\Eestec\Platform\Documents\Providers\DocumentsRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Documents\Tags\Providers\TagsRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Documents\Providers\EventsServiceProvider::class,

    // Faculties

    Angelov\Eestec\Platform\Faculties\Providers\FacultiesRepositoryServiceProvider::class,

    // Local Committees

    Angelov\Eestec\Platform\LocalCommittees\Cities\Providers\CitiesRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\LocalCommittees\Providers\LocalCommitteesRepositoryServiceProvider::class,

    // EESTEC Events

    Angelov\Eestec\Platform\Events\Providers\EventsRepositoryServiceProvider::class,
    Angelov\Eestec\Platform\Events\Comments\Providers\CommentsRepositoryServiceProvider::class

];
