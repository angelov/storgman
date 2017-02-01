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

    Angelov\Storgman\Core\Providers\AppServiceProvider::class,
    Angelov\Storgman\Core\Providers\BusServiceProvider::class,
    Angelov\Storgman\Core\Providers\ConfigServiceProvider::class,
    Angelov\Storgman\Core\Providers\RouteServiceProvider::class,
    Angelov\Storgman\Core\FileSystem\FileSystemsRegistryServiceProvider::class,

    // Members

    Angelov\Storgman\Members\Providers\MembersRepositoryServiceProvider::class,
    Angelov\Storgman\Members\Photos\Providers\PhotosRepositoryServiceProvider::class,
    Angelov\Storgman\Members\Providers\MembersPopulatorServiceProvider::class,
    Angelov\Storgman\Members\Providers\EventsServiceProvider::class,
    Angelov\Storgman\Members\Providers\ViewComposersServiceProvider::class,

    // Social Profiles

    Angelov\Storgman\Members\SocialProfiles\Providers\SocialProfilesRepositoryServiceProvider::class,

    // Membership (Fees)

    Angelov\Storgman\Membership\Providers\FeesRepositoryServiceProvider::class,
    Angelov\Storgman\Membership\Providers\EventsServiceProvider::class,

    // Meetings

    Angelov\Storgman\Meetings\Providers\MeetingsRepositoryServiceProvider::class,
    Angelov\Storgman\Meetings\Providers\EventsServiceProvider::class,
    Angelov\Storgman\Meetings\Attachments\Providers\AttachmentsRepositoryServiceProvider::class,
    Angelov\Storgman\Meetings\Attachments\Providers\EventsServiceProvider::class,
    Angelov\Storgman\Meetings\Attachments\Packaging\PackagingManagerServiceProvider::class,

    // Documents

    Angelov\Storgman\Documents\Providers\DocumentsRepositoryServiceProvider::class,
    Angelov\Storgman\Documents\Tags\Providers\TagsRepositoryServiceProvider::class,
    Angelov\Storgman\Documents\Providers\EventsServiceProvider::class,

    // Faculties

    Angelov\Storgman\Faculties\Providers\FacultiesRepositoryServiceProvider::class,

    // Local Committees

    Angelov\Storgman\LocalCommittees\Cities\Providers\CitiesRepositoryServiceProvider::class,
    Angelov\Storgman\LocalCommittees\Providers\LocalCommitteesRepositoryServiceProvider::class,

    // EESTEC Events

    Angelov\Storgman\Events\Providers\EventsRepositoryServiceProvider::class,
    Angelov\Storgman\Events\Comments\Providers\CommentsRepositoryServiceProvider::class

];
