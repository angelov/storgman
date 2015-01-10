<?php namespace Angelov\Eestec\Platform\Http\Middleware;

use Angelov\Eestec\Platform\Exception\NotAllowedException;
use Closure;

class AjaxOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @throws NotAllowedException
     * @return mixed
     */
	public function handle($request, Closure $next)
	{
        if (!$request->ajax()) {
            throw new NotAllowedException();
        }

        return $next($request);
	}

}
