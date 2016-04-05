<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController {
	
	public $pageSize;
	
	public function __construct() {
		// $this->middleware('athu');
		
		if(isset($_GET['page_size']) && $_GET['page_size'] < self::MAX_PAGE_SIZE ){
			$this->pageSize = $_GET['page_size']; 
		}else {
			$this->pageSize = self::PAGE_SIZE;
		}
// 		parent::__construct();
	}
	
	const PAGE_SIZE = 100;
	const MAX_PAGE_SIZE = 150;
	
	//
	// 100 => 'Continue',
	// 101 => 'Switching Protocols',
	const SUCCESS_OK = 200; // 200 => 'OK',
	// 201 => 'Created',
	const REQUEST_ACCEPTED = 202;	// 202 => 'Accepted',
	// 203 => 'Non-Authoritative Information',
	// 204 => 'No Content',
	// 205 => 'Reset Content',
	// 206 => 'Partial Content',
	// 300 => 'Multiple Choices',
	// 301 => 'Moved Permanently',
	// 302 => 'Found',
	// 303 => 'See Other',
	// 304 => 'Not Modified',
	// 305 => 'Use Proxy',
	// 306 => '(Unused)',
	// 307 => 'Temporary Redirect',
	// 400 => 'Bad Request',
	const UN_AUTHORIZED = 401;	// 401 => 'Unauthorized',
	// 402 => 'Payment Required',
	const FORBIDDEN = 403; // 403 => 'Forbidden',
	const NO_ENTITY = 404; // 404 => 'Not Found',
	// 405 => 'Method Not Allowed',
	const NOT_ACCEPTABLE = 406; // 404 => 'Not Found',
	
	// 406 => 'Not Acceptable',
	// 407 => 'Proxy Authentication Required',
	// 408 => 'Request Timeout',
	// 409 => 'Conflict',
	// 410 => 'Gone',
	// 411 => 'Length Required',
	// 412 => 'Precondition Failed',
	// 413 => 'Request Entity Too Large',
	// 414 => 'Request-URI Too Long',
	// 415 => 'Unsupported Media Type',
	// 416 => 'Requested Range Not Satisfiable',
	// 417 => 'Expectation Failed',
	// 500 => 'Internal Server Error',
	// 501 => 'Not Implemented',
	// 502 => 'Bad Gateway',
	// 503 => 'Service Unavailable',
	// 504 => 'Gateway Timeout',
	// 505 => 'HTTP Version Not Supported'
	
	


	
	private static final function statusMessage($option) {
		$status = array ();
		$ok = 'OK';
		$error = 'ERROR';
		
		switch ($option) {
			
			case self::SUCCESS_OK :
				$status ['message'] = 'Success';
				$status ['status'] = $ok;
				$status ['code'] = '200';
				break;
				
			case self::REQUEST_ACCEPTED :
				$status ['message'] = 'Request Accepted';
				$status ['status'] = $ok;
				$status ['code'] = '202';
				break;
			
			case self::UN_AUTHORIZED :
				$status ['message'] = 'Login is Required!';
				$status ['status'] = $error;
				$status ['code'] = '401';
				break;
				
			case self::FORBIDDEN :
				$status ['message'] = 'Forbidden Access!';
				$status ['status'] = $error;
				$status ['code'] = '403';
				break;
				
			case self::NO_ENTITY :
				$status ['message'] = 'No Such Entity!';
				$status ['status'] = $error;
				$status ['code'] = '404';
				break;

			case self::NOT_ACCEPTABLE :
				$status ['message'] = 'Unacceptable values';
				$status ['status'] = $error;
				$status ['code'] = '406';
				break;
				
			
			default :
				$status ['message'] = 'Success';
				$status ['status'] = $ok;
				$status ['code'] = '200';
				break;
		}
		return $status;
	}
	
	
	
private static function json_to_array(&$array)
{
	if(is_array($array)){
		foreach ($array as $key => &$value) {

			if($key=='dashboard_count' || $key=='dashboard_date'){
				$value = explode(',', $value);
			}else{
				if(is_string($value)){
					$res = json_decode($value,true);		
					if(is_array($res))
						$value = $res;
				}elseif(is_array($value))
					self::json_to_array($value);
			}
		}
	}
	else{
			$array = json_decode($array,true);
			if(is_array($array))
				self::json_to_array($array);
	}
}
	
	public static final function sendResponse($responseData, $status = self::SUCCESS_OK, $message = null) {
		
		$responseData = response ()->json ( $responseData )->getData ( true );
		
// 		self::json_to_array($responseData);
		array_walk_recursive($responseData,'toJson');		
		$response = array ();
		
		if (! empty ( $responseData ) && $status == self::SUCCESS_OK) {
			
			$response = self::statusMessage ( $status );
			if (! is_null ( $message )) {
				$response ['message'] = $message;
			}
			$response ['result'] = $responseData;
		} else {
			
			if ($status != self::SUCCESS_OK) {
				$response = self::statusMessage ( $status );
			} else {
				$response = self::statusMessage ( self::NO_ENTITY );
			}
			
			if (! is_null ( $message )) {
				$response ['message'] = $message;
			}
			$response ['result'] = $responseData;
		}
		
		return response ()->json ( $response )
// 		->headers->add(array('Access-Control-Allow-Origin', '*'))
		;
	}
		
	public static function getResponseArr(Request $request) {
	
		return json_decode($request->getContent(),true);
	}
	
	protected function addToMailList(array $persion , $group, $list='4b6d309d13', $mailer = 'mailchimp'){
		
		$data  = array(
				'email_address' => $persion['email'],
				'status' => 'subscribed'				
		);		
		$intrests =  array(
			"5f833cb0fc" => false,
			"a651565436" => false,
			"23179fea26" => false,
			"68c8e3c847" => false,
			"d761a47584" => false	
		);
		
//  		{
// 			"email_address":"mcvka@jokes.com",
// 			"status":"subscribed",
// 			"merge_fields": {
// 			"FNAME": "test",
// 			"LNAME": "test1"
// 			},
// 			"interests":{
// 			"5f833cb0fc": false,
// 			"a651565436": false,
// 			"23179fea26": false,
// 			"68c8e3c847": false,
// 			"d761a47584": false
// 			}
// 		}
		
		
		$city = array(		
			"delhi"	=> "5f833cb0fc",
			"mumbai" => "a651565436",
			"pune"	=> "23179fea26",
			"bangalore"	=> "68c8e3c847",
			"other"	=> "d761a47584"				
		);
		
		$group = strtolower($group);
		
		switch ($group){
			case 'delhi':
			case 'new delhi':
			
				$intrests[$city['delhi']] = true;
			break;
			case 'mumbai':
			case 'bombay':
			
				$intrests[$city['mumbai']] = true;
			break;
			
			case 'pune':
			case 'puna':
					
				$intrests[$city['pune']] = true;
			break;
			
			case 'bangalore':
			case 'Bengaluru':
						
				$intrests[$city['bangalore']] = true;
			
			break;

			default:							
				$intrests[$city['other']] = true;
				
		}
		
		$data['interests']=$intrests;
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
				CURLOPT_URL => "https://us11.api.mailchimp.com/3.0/lists/$list/members",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_HTTPHEADER => array(
						"authorization: Basic U2h1Y2hpciBTdXJpOjBmNmJiMDdmMTkwYTIxMWMzMGM3MDEyOTg1Y2YwNWY3LXVzMTE=",
						"cache-control: no-cache",
						"content-type: application/json"
				),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) {
// 			return  false;
// 			echo "cURL Error #:" . $err;
		} else 
// 			echo $response;
		
		
		return true;
		}
	
}




