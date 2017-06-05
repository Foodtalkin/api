<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController {
	
	
	protected $privilageUser;
	
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
	const PAYMENT_REQUIRED = 402;
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
	
	
	public static final function Instamojo($query='',  $uri, $method = "POST", $head=[]){
		
		
		$curl = curl_init();
		
		$head[] = 'Content-Type: application/json';
		$head[] = 'cache-control: no-cache';
		
		curl_setopt_array($curl, array(
				
				CURLOPT_URL => "https://test.instamojo.com".$uri,
// 				CURLOPT_URL => "https://test.instamojo.com/oauth2/token/?client_id=FFNcaPNlSaKlf7kmONUBhYMAIeUmqw7owwkOvkBO&client_secret=3RaSSxYEtcnOyec8UdHsqIVHXtvOf3R14fH0ejxgsbNRpMWnVnFasK2ACAgIIRIddd27dQoQ4EHJwQyMQJVQ2cpbLIEh84oTtKW1kdgFDAAbwGD17EOkgI1QYIloNvDe&grant_type=client_credentials",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $query,
				CURLOPT_HTTPHEADER => $head
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $response;
		}
		
		
		
		
		
		
		
		
		$ch = curl_init();
		// 	curl_setopt($ch, CURLOPT_PORT, "9200");
		curl_setopt($ch, CURLOPT_URL,"https://test.instamojo.com".$uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		// 	curl_setopt($ch, CURLOPT_ENCODING, "");
		// 	curl_setopt($ch, CURLOPT_MAXREDIRS,10);
		// 	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		// 	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		
		if(!empty($query))
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query."\n");
		
			
			$head[] = 'Content-Type: application/json';
			$head[] = 'cache-control: no-cache';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
			// 		curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, true);
			
			$response = curl_exec ($ch);
			
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);
			
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			$err = curl_error($ch);
			curl_close ($ch);
			
			$result = json_decode($body);
			
// 			if($http_code > 299 OR $http_code < 200 )
// 			{
// 				if($http_code>0)
// 					abort($http_code, isset($result->Message)?$result->Message:' Elastic Node not responding!' );
// 					else
// 						abort('500',' Elastic Node not responding!' );
// 			}
			
			
			if ($err) {
				return "cURL Error #:" . $err;
			} else {
				return $result;
			}
	}
	
	
	public static final function es($query='',  $uri, $method = "GET"){
	
		$ch = curl_init();
		// 	curl_setopt($ch, CURLOPT_PORT, "9200");
		curl_setopt($ch, CURLOPT_URL,"https://search-foodtalk-es-ttze4pfv56f5ylxvs7xcha23ri.ap-southeast-1.es.amazonaws.com:443".$uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		// 	curl_setopt($ch, CURLOPT_ENCODING, "");
		// 	curl_setopt($ch, CURLOPT_MAXREDIRS,10);
		// 	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		// 	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	
		if(!empty($query))
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query."\n");
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/text','cache-control: no-cache'));
// 		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		
		$response = curl_exec ($ch);
		
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		$err = curl_error($ch);
		curl_close ($ch);
	
		$result = json_decode($body);
		
		if($http_code!='200')
		{
			if($http_code>0)
				abort($http_code, isset($result->Message)?$result->Message:' Elastic Node not responding!' );
			else
				abort('500',' Elastic Node not responding!' );
		}
		
		
		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $result;
		}
	}
	
	
	
private static function json_to_array(&$array)
{
	if(is_array($array)){
		foreach ($array as $key => &$value) {

			if($key=='dashboard_count' || $key=='dashboard_date'){
				if(is_string($value))
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
		if(is_array($responseData))
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
		
		$response['api'] = $_SERVER['REQUEST_URI'];
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




