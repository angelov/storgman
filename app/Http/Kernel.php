<?php namespace Angelov\Eestec\Platform\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
	];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => 'Angelov\Eestec\Platform\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest' => 'Angelov\Eestec\Platform\Http\Middleware\RedirectIfAuthenticated',
        'boardMember' => 'Angelov\Eestec\Platform\Http\Middleware\BoardMembersOnlyMiddleware',
        'ajax' => 'Angelov\Eestec\Platform\Http\Middleware\AjaxOnlyMiddleware',
        'boardMemberOrSelf' => 'Angelov\Eestec\Platform\Http\Middleware\BoardMembersOrSelfMiddleware',
    ];

}
