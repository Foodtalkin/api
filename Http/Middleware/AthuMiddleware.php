<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Controller;

class AthuMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') == 'local') {
// 		if(env('APP_ENV') == 'local' || env('APP_ENV') == 'stage'){
            return $next ($request);
        }
        $authorized = false;
        $message = null;
        $sessionID = $request->header('APPSESSID');

        if ($request->get('APPSESSID')) {
            $sessionID = $request->get('APPSESSID');
        }

        if ($sessionID) {
            session_id($sessionID);
            session_start();
            if (isset ($_SESSION ['admin_id'])) {
                $authorized = true;
            } else {
                $message = 'Invalid/Expired Session.';
                session_destroy();
            }
        }

        if (!$authorized) {
            return Controller::sendResponse(null, Controller::UN_AUTHORIZED, $message);
        }

        return $next ($request);
    }
}
