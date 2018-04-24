<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Controller;

class PartnerMiddleware
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
            return $next ($request);
        }

        $authorized = false;
        $message = null;
        $sessionId = $request->header('APPSESSID');

        if ($sessionId) {
            session_id($sessionId);
            session_start();
            if (isset ($_SESSION ['restaurant_id'])) {
                $authorized = true;
            } else {
                $message = 'Invalid/Expired Session.';
                session_destroy();
            }
        }

        if (! $authorized) {
            return Controller::sendResponse(null, Controller::UN_AUTHORIZED, $message);
        }

        return $next ($request);
    }
}
