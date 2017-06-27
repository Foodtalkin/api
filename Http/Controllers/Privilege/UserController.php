<?php

namespace App\Http\Controllers\Privilege;

// use DB;

use App\Models\Privilege\User;
use App\Models\Privilege\Otp;
use App\Models\Privilege\Session;
use App\Models\Privilege\Subscription;
use App\Models\Privilege\SubscriptionType;
use DB;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;

use Illuminate\Database\QueryException;
use App\Models\Privilege\InstamojoRequest;
use App\Models\Privilege\InstamojoPayment;
use App\Models\Privilege\InstamojoLog;

class UserController extends Controller {


	
	public function update(Request $request) {
		
		$arr =	$request->getRawPost();
		
		$user = User::find($_SESSION['user_id']);
		
		if(isset($arr->name))
			$user->name = $arr->name;
		if(isset($arr->email))
			$user->email = $arr->email;
		if(isset($arr->gender))
			$user->gender = $arr->gender;
		if(isset($arr->preference))
			$user->preference = $arr->preference;
		if(isset($arr->dob))
			$user->dob = new \DateTime($arr->dob);
		
		$user->save();
		$user = User::find($_SESSION['user_id']);
		
		return $this->sendResponse ( $user, self::SUCCESS_OK, 'Update Success' );
	}
	
	
	// gets a user with id
	public function login(Request $request) {

		$otpMatched = false;
		$arr =	$request->getRawPost();
		
		$otp = Otp::find($arr->phone);
		
		$user = User::where('phone', 'like' , $arr->phone)->first();
		
		if($otp && $otp->otp == $arr->otp ){
			$otpMatched = true;
			
			$session = Session::firstOrNew( array('user_id'=>$user->id));
			$session_id = sha1(microtime());
			$session->session_id = $session_id;
			$session->refresh_token = sha1(microtime());
			$session->user_id = $user->id;
			$session->save();

			$user->is_verified = 1;
			$user->save();
		}
		
		$user->subscription;
		$user->session;
		
		if(!$otpMatched){
			return $this->sendResponse ( 'ERROR! : Invalid / Expired OTP',  self::NOT_ACCEPTABLE, 'Invalid / Expired OTP!');
		}
		
		$otp->delete();
		return $this->sendResponse ( $user, self::SUCCESS_OK, 'OTP Accepted' );
	}

	
	public function webhookInstamojo(Request $request) {
		
		$arr = array(
				'payment_id' => $_POST['payment_request_id'],
				'instamojo_paymant_id' => $_POST['payment_id'],
				'buyer_name'=>$_POST['buyer_name'],
				'amount'=>$_POST['amount'],
				'status'=>$_POST['status'],
				'phone'=>$_POST['buyer_phone'],
				'metadata'=>json_encode($_POST)
		);
		
		$log = InstamojoLog::create($arr);
		
		
// 		$paymentPayment= array(
// 				'payment_id'=> $_POST['payment_request_id'],
// 				'amount'=>$_POST['amount'],
// 				'status'=>$instamojo_payment['status'],
// 				'phone'=>$_POST['buyer_phone'],
// // 				'metadata'=>$instamojo_payment_info
// 		);
// 		InstamojoPayment::updateOrCreate($paymentPayment);
		
		
		return $this->sendResponse ( true );
	}
	
	
	public function subscriptionPayment(Request $request) {
		$arr =	$request->getRawPost();
		
		$type = SubscriptionType::where('id', '=',$arr->subscription_type_id)->first();
		
		if(!$type){
			return $this->sendResponse ( 'ERROR! : Invalid / subscription type',  self::NOT_ACCEPTABLE, 'ERROR! : Invalid / subscription type');
		}
		$user = User::find($_SESSION['user_id']);
		
		$uri = '/oauth2/token/?'.
		'client_id=EJIVonhms8TomBf0mEMPUmIJWTnHOgJzuD9ojA44'.
		'&client_secret=KNnTI3fbdTF8rah9IVzVVv3YR3M1ECHmQ7WAKpCLobI9u1cLuI726rGjBHSqSMuCfSIOPalx1QaTYmRa47rObhBJ9pnXZuDgdsk2TYWcnk76i5LDf7sKXeV4jyOotBDK'.
		'&grant_type=client_credentials';
		
		$accessInfo = self::Instamojo('' ,$uri, 'POST');
		$access = json_decode($accessInfo, true);
		
		
		if(isset($arr->source) and 'web' == strtolower($arr->source))
			$redirect_url = "http://foodtalk.in/success.html";
		else 
			$redirect_url = "https://www.instamojo.com/integrations/android/redirect/";
		
		$_SESSION['instamojo_access_token'] = $access['access_token'];
		
		$uri = '/v2/payment_requests/';
		$post = array(
				"amount"=> $type->price,
				"purpose"=> "Annual Privilege Membership",
				"buyer_name"=> $user->name,
				"email"=> $user->email,
				"phone"=> $user->phone,
				"webhook"=> "http://api.foodtalk.in/webhook/instamojo",
				"redirect_url" => $redirect_url,
				"allow_repeated_payments"=> false,
				"send_email"=> true,
				"send_sms"=> true
		);
		
		$transactionInfo = self::Instamojo(json_encode($post), $uri, 'POST', array('Authorization: Bearer '.$access['access_token']));
		$transaction = json_decode($transactionInfo, true);

		if(isset($transaction['id'])){
			
		}else 
			return $this->sendResponse ( $transaction , self::NOT_ACCEPTABLE );
		
		$paymentRequest = array(
				'user_id'=>$_SESSION['user_id'],
				'payment_id'=> $transaction['id'],
				'subscription_type_id'=>$arr->subscription_type_id,
				'amount'=>$transaction['amount'],
				'status'=>$transaction['status'],
				'payment_url'=>$transaction['longurl'],
				'metadata'=>$transactionInfo
		);
		
		InstamojoRequest::create($paymentRequest);
		
		
		$uri = '/v2/gateway/orders/payment-request/';
		$post = array( "id"=> $transaction['id']);
		
		$orderInfo = self::Instamojo(json_encode($post), $uri, 'POST', array('Authorization: Bearer '.$access['access_token']));
		$order = json_decode($orderInfo, true);
		
		$result['access_token'] = $access['access_token'];
		$result['paymentid'] = $transaction['id'];
		$result['transaction'] = $transaction;
		
		$result['order'] = $order;
		
		return $this->sendResponse ( $result );
	}
	
	
	public function subscription(Request $request) {
		
		$arr =	$request->getRawPost();
		
		$paymentRequest = InstamojoRequest::where('payment_id', '=', $arr->payment_id)->first();
		
		if(!$paymentRequest){
			return $this->sendResponse ( 'ERROR! : Invalid payment_id',  self::NO_ENTITY, 'ERROR! : Invalid payment_id');
		}
		
		
		$uri =  '/v2/payment_requests/'.$paymentRequest->payment_id.'/';
		$instamojo_payment_info = self::Instamojo('', $uri, 'GET', array('Authorization: Bearer '.$_SESSION['instamojo_access_token']));
		
		$instamojo_payment = json_decode($instamojo_payment_info, true);

		$paymentPayment= array(
				'payment_id'=> $instamojo_payment['id'],
				'amount'=>$instamojo_payment['amount'],
				'status'=>$instamojo_payment['status'],
				'phone'=>$instamojo_payment['phone'],
				'metadata'=>$instamojo_payment_info
		);
		InstamojoPayment::updateOrCreate($paymentPayment);
		
		
		if($instamojo_payment['amount'] == $paymentRequest->amount && $instamojo_payment['status'] == 'Completed' ){
			
		}else{
			return $this->sendResponse ( false,  self::PAYMENT_REQUIRED, 'ERROR! : Instamojo Payment Status : '.$instamojo_payment['status'] );
		}
		
		$subscription = Subscription::where('expiry', '>', DB::raw('now()'))->where(array('user_id'=>$_SESSION['user_id'], 'subscription_type_id'=>$paymentRequest->subscription_type_id ))->first();
	
		if($subscription){
// 			return $this->sendResponse ( 'ERROR! : already subscribed',  self::NOT_ACCEPTABLE, 'ERROR! : similar subscription is already active!');
		}else{

			$subscription = new Subscription();
			$subscription->user_id = $_SESSION['user_id'];
			$subscription->subscription_type_id = $paymentRequest->subscription_type_id;
			$subscription->city_id = $subscription->subscriptionType->city_id;
			$NewDate = Date('y-m-d 23:59:59', strtotime("+".$subscription->subscriptionType->expiry_in_days - 1 ." days"));
			$subscription->expiry = $NewDate;
			$subscription->save();
			
			$subscription= Subscription::find($subscription->id);
			
			
		}
		$result['subscription'][] = $subscription;
		return $this->sendResponse ( $result );
	}
	
	
	public function activeSubscription() {
	
		
		$subscription = Subscription::where('expiry', '>', DB::raw('now()'))->where(array('user_id'=>$session->user_id, 'subscription_type_id'=>$arr->subscription_type_id ))->get();
		$result = SubscriptionType::where('is_disabled', '=', '0')->with('city')->get();
		return $this->sendResponse ( $result );
	
	}
	
	public function avilableSubscription() {
		
		$result = SubscriptionType::where('is_disabled', '=', '0')->with('city')->get();
		return $this->sendResponse ( $result );
		
	}
	
	
	public function refreshSession(Request $request) {

		$arr =	$request->getRawPost();
		$session = Session::where( 'refresh_token','like',$arr->refresh_token)->first();
		
		if($session){
			
	// 		$session->refresh_token = sha1(microtime());
	// 		$session->user_id = $user->id;
			$session->session_id = sha1(microtime());
			$session->save();
			return $this->sendResponse ( $session );
// 			NOT_ACCEPTABLE
		}
		
		return $this->sendResponse ( $session, self::NO_ENTITY, 'Error! :  Invalid / expired refresh_token' );
	}
	
	
	public function checkUser($phone){
	 	$user = User::where('phone', 'like' , $phone)->first();
// 	 	$user['subscription'] = 
// 	 	Subscription::where('user_id','=', $user->id)->get();
// 	 	$user->subscription();
// 	 	if(!$user){
// 	 		$user = 'No such user';
// 	 	}
	 	return $this->sendResponse ( $user );
	}
	
	public function resendOTP(Request $request, $phone) {

		$result = Otp::find($phone);
		if($result){
			$OTP = $result->otp;
			$url = "https://control.msg91.com/api/sendotp.php?authkey=152200A5i7IQU959157bfe&mobile=$phone&message=Your%20Foodtalk%20Privilege%20OTP%20is%20$OTP&sender=FODTLK&otp=$OTP";
			file_get_contents($url);
			return $this->sendResponse('OTP '.$OTP.' is sent to : '.$phone);
		}
		else
			return $this->sendResponse ( false );
		
	}
	
	public function getOTP(Request $request) {
		
		$OTP = rand(1000, 9999);
		$arr =	$request->getRawPost();
		
		$otp = Otp::findOrNew($arr->phone);
		$phone = $arr->phone;
		
		$otp->otp = $OTP;
		$otp->phone = $phone;
		$url = "https://control.msg91.com/api/sendotp.php?authkey=152200A5i7IQU959157bfe&mobile=$phone&message=Your%20Foodtalk%20Privilege%20OTP%20is%20$OTP&sender=FODTLK&otp=$OTP";

		
		if(isset($arr->name)){
			
			
			try{
				
				
			$user = User::where('phone', '=', $arr->phone)->first();
				
			if(isset($arr->signup) && $arr->signup == '1'){
				
			if($user){
				return $this->sendResponse(array('error'=>'phone'),self::NOT_ACCEPTABLE, 'phone already registered');
			}
			else
				$user = new User();
			
				$user->phone=$arr->phone;

				$user->name = $arr->name;
// 				if(isset($arr->email))
				$user->email = $arr->email;
				
				if(isset($arr->dob))
					$user->dob = $arr->dob;
				
				if(isset($arr->gender))
					$user->gender = $arr->gender;
				
				$user->save();
			}			
			
			}
			catch (QueryException $e){
				return $this->sendResponse(array('error'=>'email'),self::NOT_ACCEPTABLE, $e->errorInfo[2]);
			}
		}
		
		$otp->save();
		file_get_contents($url);
		
		return $this->sendResponse('OTP '.$OTP.' is sent to : '.$arr->phone);
		
	}
	
	
	
	// list all user
	public function subscription1(Request $request) {

		
		if($for=='nonapp'){
			
			$User = User::where ( 'is_disabled', '0' )->with('score')
			->leftjoin('activity_score', 'user.facebook_id', '=', 'activity_score.facebookId')
			->where ( 'activity_score.facebookId', null )
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
			
		}
		
			
		if($for=='onapp'){

			$User = User::where ( 'is_disabled', '0' )->with('score')
			->join('activity_score', 'user.facebook_id', '=', 'activity_score.facebookId')
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
				
		}
		
		if($for=='all'){
			$User = User::where ( 'is_disabled', '0' )->with('score')
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
		}

		
		
		
// 		$User = User::where ( 'is_disabled', '0' )->with('score')		
// 		->orderBy('id', 'desc')->paginate ( $this->pageSize );
		
		
		return $this->sendResponse ( $User );
	}
	
	
	public function listAllWithCity($city = null) {
		
		$User = User::where ( 'is_disabled', '0' )->with('score')->where( 'city_id', $city )->orderBy('id', 'desc')->paginate ( $this->pageSize );		
		return $this->sendResponse ( $User );
	}
	
	
	
	
	public function tag($tags){

			$tags = urldecode($tags);
			$tags = explode(',', $tags);
			
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->join('event_participant', 'user.id', '=', 'event_participant.user_id')
			->join('events', 'events.id', '=', 'event_participant.events_id')
			->join('tags', 'events.id', '=', 'tags.events_id')
			->where(
					function($query) use ($tags){
						$first = true;
						foreach ($tags as $tag){
							if($first){
								$query->where ( 'tag_name', 'LIKE' , $tag);
								$first = false;
							}
							else
								$query->orwhere ( 'tag_name', 'LIKE' , $tag );
						}
					}
		
			)
			->groupBy('user.id')
// 			->orderBy('user.id', 'desc')
			->get();
// 			->paginate ( $this->pageSize );
			return $this->sendResponse ( $User );
		
	}
	
	public function search($text, $tags = null) {
		$text = urldecode($text);
		
		if(!is_null($tags)){
			$tags = urldecode($tags);
			$tags = explode(',', $tags);
			
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->where(
				function($query) use ($text){
					$query->where ( 'user.email', 'LIKE' , "%$text%") 
						  ->orwhere ( 'user.name', 'LIKE' , "%$text%" );
				}
			)
			->join('event_participant', 'user.id', '=', 'event_participant.user_id')
			->join('events', 'events.id', '=', 'event_participant.events_id')
			->join('tags', 'events.id', '=', 'tags.events_id')
				->where(
						function($query) use ($tags){
							$first = true;
							foreach ($tags as $tag){
								if($first){
									$query->where ( 'tag_name', 'LIKE' , $tag);
									$first = false;
								}
								else 
									$query->orwhere ( 'tag_name', 'LIKE' , $tag );
							}
						}
						
// 						'tag_name',$tag
						)
			->groupBy('user.id')
			->orderBy('user.id', 'desc')->paginate ( $this->pageSize );
		}
		else {
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->where(
					function($query) use ($text){
						$query->where ( 'user.email', 'LIKE' , "%$text%")
						->orwhere ( 'user.name', 'LIKE' , "%$text%" );
					}
			)
			->orderBy('user.id', 'desc')->paginate ( $this->pageSize );
		}
		return $this->sendResponse ( $User );
	}
	
	
	public function checkEmail(Request $request) {
		$attributes = $this->getResponseArr ( $request );
		$user = User::where ( 'email', $attributes['email'] )->first ();
		if($user){
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'This email is not avilable');
		} 
		return $this->sendResponse ( true, self::SUCCESS_OK, 'Email is avilable');
			
		
	}
	
	public function create(Request $request) {
		
		$attributes = $this->getResponseArr ( $request );
		if(!isset($attributes['facebook_id'])){
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'Invalid request, No facebook_id provided');
		}
		
		$user = User::where ( 'facebook_id', $attributes['facebook_id'] )->first ();
		if(!$user){
			
			if (isset($attributes['email'])){
				$user = User::where ( 'email', $attributes['email'] )->first ();
				if($user)
					unset($attributes['email']);
				}			
			
			$user = User::create ( $attributes );
			$user['is_new'] = true;

		}
		return $this->sendResponse ( $user );
	}
	
// 	public function update(Request $request, $id) {
// 		$user = User::find ( $id );
// 		$user->update ( $this->getResponseArr ( $request ) );
		
// 		return $this->sendResponse ( $user );
// 	}
	public function delete($id) {
		$user = User::find ( $id );
		
		if ($user) {
			$user->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	public function participation(Request $request, $id, $ptype) {

		$requestArr = $this->getResponseArr ( $request );
		$participant = array ();
		
		
		if ($ptype=='rsvp'){
			$ep  = EventParticipant::where(array('events_id'=>$requestArr ['events_id'], 'user_id' => $id))->first();
			
			if(!empty($ep)){
				return $this->sendResponse ( $ep, self::NOT_ACCEPTABLE, "You already participated" );
			}
		}else{
			$transaction_id = md5(time().'_'.$requestArr ['events_id'].'_'.$id) ;
			$participant ['transaction_id'] = $transaction_id;
		}
		
		$event = Events::find ( $requestArr ['events_id'] );		
		
		
		$participant ['subscribe'] = 0;
		
		if (isset ( $requestArr ['payment_id'] ))
			$participant ['payment_id'] = $requestArr ['payment_id'];
		
		if (isset ( $requestArr ['payment_method'] ))
			$participant ['payment_method'] = $requestArr ['payment_method'];
		
		if (isset ( $requestArr ['quantity'] ))
			$participant ['quantity'] = $requestArr ['quantity'];
		
		if (isset ( $requestArr ['payment'] ))
			$participant ['payment'] = $requestArr ['payment'];
		
		if (isset ( $requestArr ['email'] ))
			$participant ['email'] = $requestArr ['email'];
		
		if (isset ( $requestArr ['subscribe'] ) && $requestArr ['subscribe'] =='1' && isset ( $requestArr ['email'] ) && strlen($requestArr ['email']) > 2 ) {			
			$this->addToMailList(array('email'=>$requestArr ['email']), $event->location);
			$participant ['subscribe'] = 1;
		}
		
		if (isset ( $requestArr ['contact'] ))
			$participant ['contact'] = $requestArr ['contact'];
		
		if (isset ( $requestArr ['metadata'] ))
			$participant ['metadata'] = $requestArr ['metadata'];
		
		if (isset ( $requestArr ['response'] ))
			$participant ['response'] = json_encode ( $requestArr ['response'] );
		
		if (isset ( $requestArr ['source'] ))
			$participant ['source'] = json_encode ( $requestArr ['source'] );
		
// 		$result = User::find ( $id )->events ()->save ( $event, $participant );

		$user = User::find ( $id );
		if($user)
			$result = $user->events ()->save ( $event, $participant );
		else {
			$participant['events_id'] = $requestArr ['events_id'];
			$participant['user_id'] = $id;
			$result = EventParticipant::create($participant);
// 			$result = $user->events ()->save ( $event, $participant );
		}
		
		if(isset($transaction_id))
			$result['transaction_id'] = $transaction_id;
		
		return $this->sendResponse ( $result );
	}
}
?>
