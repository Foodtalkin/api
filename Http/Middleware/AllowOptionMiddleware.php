<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AllowOptionMiddleware
{
	
	/**
	 * Access Control Headers.
	 * @var array
	 */
	protected $headers = [
// 			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
			'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin, Accept, APPSESSID',
			'Access-Control-Allow-Credentials'=> 'true',
// 			'Access-Control-All-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Key'
	];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	
    	if ($request->isMethod('options')) {
    		return $this->setCorsHeaders(new Response('OK', 200));
    	}
        return $next($request);
    }
    
    protected function setCorsHeaders($response)
    {
    	foreach ($this->headers as $key => $value) {
    		$response->header($key, $value);
    	}
    	return $response;
    }
}
