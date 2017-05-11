<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Privilege;
use App\Http\Controllers\Controller;
use App\Models\Privilege\Session;
use DB;
// use App\Http\Controllers\Privilege\UserController;

class PrivilageUserMiddleware {
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
		$sessionID = $request->get( 'sessionid' );
		
// 			echo "PrivilageUserMiddleware\n";
		if ($sessionID) {
			session_id ( $sessionID );
			session_start();
			
// 			session_destroy();
// 			die('dead');
			
			if (isset ($_SESSION ['user_id'] )) {
				$authorized = true;
			}else{
				
				$DBsession = Session::
				where('session_id',  $sessionID)
				->where('created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 7 DAY)'))
				->first();
				if($DBsession){
					$_SESSION ['user_id'] = $DBsession->user_id;
					$_SESSION ['session_id'] = $DBsession->session_id;
					
				}else{
					$message = 'Invalid/Expired Session.';
					session_destroy();
					return Controller::sendResponse ( null, Controller::UN_AUTHORIZED , $message);
				}
				
			}
		}
		
		return $next ( $request );
	}
}
