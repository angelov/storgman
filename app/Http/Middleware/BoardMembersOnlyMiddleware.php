<?php namespace Angelov\Eestec\Platform\Http\Middleware;

use Angelov\Eestec\Platform\Entity\Member;
use Closure;
use Illuminate\Contracts\Routing\Middleware;

class BoardMembersOnlyMiddleware implements Middleware
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
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
        if (!$this->member->isBoardMember()) {
            return \Redirect::to('/');
        }
	}

}
