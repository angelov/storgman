<?php

namespace Angelov\Eestec\Platform\Http\Middleware;

use Angelov\Eestec\Platform\Entities\Member;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class BoardMembersOrSelfMiddleware
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
        $id = $request->route()->getParameter('id');

        if (!$member->isBoardMember() && $id != $member->id) {
            return \Redirect::to('/');
        }

        return $next($request);
    }

}
