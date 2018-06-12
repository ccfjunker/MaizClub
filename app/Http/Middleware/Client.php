<?php namespace App\Http\Middleware;

use App\Http\Controllers\BaseController;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Client {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!BaseController::isClient())
			throw new AccessDeniedHttpException('unauthorized_client');
		return $next($request);
	}

}
