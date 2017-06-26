<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;

class ES 
{

// 	protected $table = 'city';
	
	public static final function request($query='',  $uri, $method = "GET"){
		
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
}