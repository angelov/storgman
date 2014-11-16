<?php namespace Angelov\Eestec\Platform\Http\Middleware;

use Angelov\Eestec\Platform\Exception\NotAllowedException;
use Closure;
use Illuminate\Contracts\Routing\Middleware;

class AjaxOnlyMiddleware implements Middleware
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if (!$request->ajax()) {
            throw new NotAllowedException();
        }
	}

}
