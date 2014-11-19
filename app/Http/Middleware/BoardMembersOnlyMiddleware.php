<?php namespace Angelov\Eestec\Platform\Http\Middleware;

use Angelov\Eestec\Platform\Entity\Member;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\Middleware;

class BoardMembersOnlyMiddleware implements Middleware
{
    protected $guard;

    public function __construct(Guard $auth)
    {
        $this->guard = $auth;
    }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        /** @var Member $member */
        $member = $this->guard->user();

        if (!$member->isBoardMember()) {
            return \Redirect::to('/');
        }

        return $next($request);
	}

}
