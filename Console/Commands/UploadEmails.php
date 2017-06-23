<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use DB;
use App\Models\Privilege\User;
use App\Models\Privilege\Subscription;


class UploadEmails extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'email:upload';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send drip e-mails to a user';
	
	
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
// 		non paid users
		$users =  User::select('name as first_name', 'user.email', DB::raw('IF(subscription.id >0 , IF(subscription.expiry > now(),  "active", "expired") ,  "inactive") as status'))
		->leftjoin('subscription', 'user.id', '=', 'subscription.user_id')
		->whereNull('subscription.id')
		->where('user.is_updated', '0')
// 		->toSql();
		->get();
		
		if(!empty($users)){
			$response =  $this->sendgrid('contactdb/recipients', $users->toJson());
			if (isset($response['persisted_recipients'])){
				$res =  $this->sendgrid('contactdb/lists/1528632/recipients', json_encode($response['persisted_recipients']));
				User::where('is_disabled','0')->update(['is_updated'=>1]);
			}
		}
		
// 		paid users
		$Subscribedusers =  User::select('name as first_name', 'user.email', DB::raw('IF(subscription.id >0 , IF(subscription.expiry > now(),  "active", "expired") ,  "inactive") as status'))
		->join('subscription', 'user.id', '=', 'subscription.user_id')
		->where('subscription.is_updated', '0')
		->get();
		
		if(!empty($Subscribedusers)){
			$response =  $this->sendgrid('contactdb/recipients', $Subscribedusers->toJson());
			if (isset($response['persisted_recipients'])){
				$res =  $this->sendgrid('contactdb/lists/1528632/recipients', json_encode($response['persisted_recipients']));
				Subscription::where('is_disabled','0')->update(['is_updated'=>1]);
			}
		}
	}
	
	
	public function sendgrid($api, $body = '', $method = 'POST'){
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.sendgrid.com/v3/".$api,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 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",
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