<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Role
{
	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
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

		if ($this->auth->guest()) {

				return redirect()->guest('login')
					->with(['login_required' => __('auth.login_required')]);

		} else if (! $this->auth->user()->role) {
			return redirect('/');

		} else if ($request->route()->getName() != 'dashboard'
					&& ! $this->auth->user()->hasPermission($request->route()->getName())
					&& $request->isMethod('get')
					) {
						abort(403);

				} else if ($this->auth->user()->role()->permissions == 'limited_access'
						&& $request->isMethod('post')
					) {

						if ($request->expectsJson()) {
							return response()->json([
									'errors' => ['error' => __('admin.unauthorized_action') ],
							]);
						}

						return redirect()->back()->withUnauthorized(__('admin.unauthorized_action'));
				}

		return $next($request);
	}

}
