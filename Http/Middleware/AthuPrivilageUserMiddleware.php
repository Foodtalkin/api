<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Privilege;
use App\Http\Controllers\Controller;
use App\Models\Privilege\Session;
use DB;
use App\Models\Privilege\Subscription;
// use App\Http\Controllers\Privilege\UserController;

class AthuPrivilageUserMiddleware {
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
		
		
// 		echo "AthuPrivilageUserMiddleware\n"; 
		
		if(boolval($sessionID) and isset ($_SESSION ['user_id'])){
			
// 			$DBsession = Session::find($sessionID);

			$DBsession = Session::
			where('session_id',  $sessionID)
			->where('created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 30 DAY)'))
			->first();
			
			$subscription = Subscription::where('user_id', $DBsession->user_id)
			->where('expiry', '>', DB::raw('NOW()'))
			->first();

			if($DBsession and $subscription){
				$authorized = true;
			}else{
				$message = 'Invalid/Expired Session or Inactive/Expired Subscription';
				session_destroy();
			}
		}
		
		if (! $authorized) {
			return Controller::sendResponse ( null, Controller::UN_AUTHORIZED , $message);
		}
		
		return $next ( $request );
	}
}
