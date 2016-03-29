<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers;
use App\Http\Controllers\Controller;

class AthuMiddleware {
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param \Closure $next        	
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		$authorized = false;
		$message = null;
		$sessionID = $request->header ( 'APPSESSID' );
		
		if ($sessionID) {
			session_id ( $sessionID );
			session_start();
			if (isset ($_SESSION ['admin_id'] )) {
				$authorized = true;
			}else{
				$message = 'Invalid/Expired Session.';
				session_destroy();
			}
		}
		
		if (! $authorized) {
			return Controller::sendResponse ( null, Controller::UN_AUTHORIZED , $message);
		}
		
		return $next ( $request );
	}
}
