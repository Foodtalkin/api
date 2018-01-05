<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;

class ParsePush
{
	
	public static function send($data, $method = 'POST'){
		
		$data['_ApplicationId']="ftp";
		$data['_MasterKey']="parse@ftp";
		$body = json_encode($data);
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
				CURLOPT_PORT => "1337",
				CURLOPT_URL => "http://foodtalk.in:1337/parse/push",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_HTTPHEADER => array(
						"cache-control: no-cache",
						"content-type: application/json",
				),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) {
			echo "cURL Error #:" . $err;
			return false;
		} else {
			return json_decode($response, true);
		}
		
	}
	
}