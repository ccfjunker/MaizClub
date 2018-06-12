<?php

namespace App\Http\Middleware;

use App\Http\Controllers\BaseController;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Admin
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
        if(!BaseController::isAdmin())
        {
            if ($request->ajax() || $request->wantsJson()) {
                throw new AccessDeniedHttpException('unauthorized');
            } else {
                return redirect()->guest('/');
            }
        }
        return $next($request);
    }
}
