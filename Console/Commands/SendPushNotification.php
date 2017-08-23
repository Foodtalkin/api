<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use DB;
use App\Models\Privilege\User;
use App\Models\Privilege\Subscription;
use App\Models\Privilege\PushNotification;


class SendPushNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'push:send';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'send all pending push notification to FoodTalkPrivilage members ';
	
	
	/**
	 * Create a new command instance.
	 *
	 * @param  DripEmailer  $drip
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$result = PushNotification::where(array('is_disabled'=>'0', 'status'=>'0'))->where('push_time', '<', DB::raw('now()'))->get();
		if(!empty($result)){
			foreach ($result as $push){
				
				echo $push->id.' =>  '. $push->push_time.' _ ARRAY ';
				$data = json_decode($push->push, true);
				echo $this->sendpush($data);	
				echo "\n";
				
				
			}
		}
	}
	
	
	public function sendpush($data, $method = 'POST'){
		
		$data['_ApplicationId']="ftp";
		$data['_MasterKey']="parse@ftp";
		$body = json_encode($data);
		
// 		return true;
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