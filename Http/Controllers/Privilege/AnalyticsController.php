<?php

namespace App\Http\Controllers\Privilege;

use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\DBLog;
use App\Models\Privilege\User;
use App\Models\Privilege\Offer;
use App\Models\Privilege\OfferRedeemed;
// use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller {
	
	public function transactions(Request $request){

		if(isset($_GET['from'])){

			if (isset($_GET['to'])){
				$s1 = ' and paytm_order_status.created_at > "'.$_GET['from'].'" and paytm_order_status.created_at < "'.$_GET['to'].'" ';
				$s2 = ' and exp_purchases.created_at > "'.$_GET['from'].'" and exp_purchases.created_at < "'.$_GET['to'].'" ';
				
			}else 
			{
				$s1 = ' and paytm_order_status.created_at > "'.$_GET['from'].'" ';
				$s2 = ' and exp_purchases.created_at > "'.$_GET['from'].'" ';
			}
		}else {
			
			$s1 = ' and paytm_order_status.created_at >= DATE(NOW()) - INTERVAL 7 DAY ';
			$s2 = ' and exp_purchases.created_at >= DATE(NOW()) - INTERVAL 7 DAY ';
		}
		
	 	$sql = 'SELECT "subscription" as title, paytm_order_id as order_id, user_id, txn_id, txn_amount, paytm_order_status.created_at, user.name, user.email, user.phone
		FROM `paytm_order_status`
		INNER JOIN paytm_order on paytm_order.id = `paytm_order_status`.`paytm_order_id` 
		INNER JOIN user on user.id = paytm_order.user_id  
		WHERE paytm_order_status.payment_status = "TXN_SUCCESS" '.$s1.'
		UNION
		SELECT experiences.title, order_id, exp_purchases_order.user_id, txn_id, exp_purchases_order.txn_amount, exp_purchases.created_at, user.name, user.email, user.phone  
		FROM `exp_purchases`
		INNER JOIN exp_purchases_order on exp_purchases.order_id = exp_purchases_order.id
		INNER JOIN user on user.id = exp_purchases_order.user_id
		INNER JOIN experiences on exp_purchases_order.exp_id = experiences.id  
		WHERE exp_purchases.payment_status = "TXN_SUCCESS" '.$s2;
	 	
	 	$result = DB::connection('ft_privilege')->select( DB::raw($sql) );
	 	
	 	return $this->sendResponse ( $result, self::SUCCESS_OK_NO_CONTENT);
	}
	
	
	public function users(Request $request ,$days = 7){
		
		
		$overall = DB::connection('ft_privilege')->select( DB::raw('select IF( subscription.id is null, "unpaid",  IF( subscription_type_id = 3,  "trial", "paid") ) as status, count(1) as count  from user LEFT JOIN subscription on subscription.user_id = user.id  GROUP by status'));
		
// 		$lastdays = DB::connection('ft_privilege')->select( DB::raw('select IF( subscription.id is null, "unpaid", "paid" ) as status, count(1) as count  from user LEFT JOIN subscription on subscription.user_id = user.id where user.created_at >= DATE(NOW()) - INTERVAL '.$days.' DAY GROUP by status'));
		
		DB::connection('ft_privilege')->statement('SET @i=0');
		$total = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(created_at,"%Y-%m-%d") as onbord from user GROUP by onbord) as u on u.onbord = calander.theday'));

		DB::connection('ft_privilege')->statement('SET @i=0');
		$paid = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(subscription.created_at,"%Y-%m-%d") as onbord from user LEFT JOIN subscription on subscription.user_id = user.id WHERE subscription.subscription_type_id = "1" GROUP by onbord ) as u on u.onbord = calander.theday'));

		DB::connection('ft_privilege')->statement('SET @i=0');
		$trial = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(subscription.created_at,"%Y-%m-%d") as onbord from user LEFT JOIN subscription on subscription.user_id = user.id WHERE subscription.subscription_type_id = "3" GROUP by onbord ) as u on u.onbord = calander.theday'));
		
		DB::connection('ft_privilege')->statement('SET @i=0');
		$unpaid = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(user.created_at,"%Y-%m-%d") as onbord from user LEFT JOIN subscription on subscription.user_id = user.id WHERE subscription.id is null GROUP by onbord ) as u on u.onbord = calander.theday'));
		
		$cnt = 0;
		foreach ($overall as $val){
			$cnt = $cnt + $val->count;
		}
		
		array_unshift($overall, array(
				'status'=>'all',
				'count' => (string) $cnt
				)
		);
		$result = array(
				'overall'=>$overall,
// 				'latest' => $lastdays,
				'datewise'=>array(
						'total'=>$total,
						'paid'=>$paid,
						'trial'=>$trial,
						'unpaid'=>$unpaid,
				)
		);
		return $this->sendResponse ( $result );
	}
	
	public function offers(Request $request ,$days = 7){
		
// 		$overall = DB::connection('ft_privilege')->select( DB::raw(''));

		$overall = DB::connection('ft_privilege')->select( DB::raw('SELECT count(offer_id) count, offer.title ,offer.id FROM offer left JOIN `offer_redeemed` on offer.id = offer_id group by offer.id'));
		
		$result = array(
				'overall'=>$overall
		);
		$offers = Offer::where('is_disabled', '0')->get();
		
		foreach ($offers as $offer){
			DB::connection('ft_privilege')->statement('SET @i=0');
			$result['datewise'][$offer->id] = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(offer_redeemed.created_at,"%Y-%m-%d") as redeemed_on from offer_redeemed left JOIN `offer` on offer.id = offer_id WHERE offer.id = '.$offer->id.' GROUP by redeemed_on) as o on o.redeemed_on = calander.theday'));
		}
		return $this->sendResponse ( $result );
	}
	

	public function restaurants(Request $request ,$top = 3, $days = 30){
		
		$result=array();
		
		$result['restaurants'] = DB::connection('ft_privilege')->select( DB::raw('SELECT COUNT(1) count FROM `restaurant` WHERE is_disabled = 0'));
		
		$result['outlet'] = DB::connection('ft_privilege')->select( DB::raw('SELECT COUNT(1) as count FROM `outlet` , restaurant WHERE restaurant.id = outlet.resturant_id and restaurant.is_disabled = 0 AND outlet.is_disabled=0'));

		$result['top'] = DB::connection('ft_privilege')->select( DB::raw('SELECT count(1) count , outlet.id, outlet.name FROM `outlet` , offer_redeemed WHERE outlet.id = offer_redeemed.outlet_id GROUP BY outlet.id  
ORDER BY `count`  DESC LIMIT '.$top));
		
		$result['latest_top'] = DB::connection('ft_privilege')->select( DB::raw('SELECT count(1) count , outlet.id, outlet.name FROM `outlet` , offer_redeemed WHERE outlet.id = offer_redeemed.outlet_id AND offer_redeemed.created_at >= DATE(NOW()) - INTERVAL '.$days.' DAY GROUP BY outlet.id ORDER BY `count`  DESC LIMIT '.$top));
		
		return $this->sendResponse ( $result );
	}
	
	
	// gets a user with id
	public function get(Request $request, $entity, $id = false) {
		
		$where['entity_table'] = $entity;
		
		if($id)
			$where['entity_id'] = $id;
		
		$result =  DBLog::where($where)
		->paginate(DBLog::PAGE_SIZE);		
		
		return $this->sendResponse ( $result );
	}
	

	public function top_users(Request $request ,$top = 5, $days = 30){

		$result=array();
		$result['top'] = DB::connection('ft_privilege')->select( DB::raw('SELECT count(1) count , user.id, user.name FROM `user` , offer_redeemed WHERE user.id = offer_redeemed.user_id GROUP BY user.id ORDER BY `count`  DESC LIMIT '.$top));
		$result['latest_top'] = DB::connection('ft_privilege')->select( DB::raw('SELECT count(1) count , user.id, user.name FROM `user` , offer_redeemed WHERE user.id = offer_redeemed.user_id AND offer_redeemed.created_at >= DATE(NOW()) - INTERVAL '.$days.' DAY GROUP BY user.id ORDER BY `count`  DESC LIMIT '.$top));
		
		return $this->sendResponse ( $result );
		
	}
	
	
	public function signups(Request $request) {
		
		$query = user::select('user.id', 'name', 'phone', 'user.email', 'gender', DB::raw('IF(expiry is null ,"unpaid",  IF( subscription_type_id = 3,  "trial", "paid")) as status'), 'user.created_at as signup_on')
		->leftjoin('subscription', 'user.id', '=','subscription.user_id')
// 		->whereNull('subscription.expiry')
		->where('user.is_verified', '=', '1')
		->orderBy('user.created_at', 'desc');
		
		if(isset($_GET['after']))
			$query->where('user.created_at', '>', $_GET['after']);
			
			$result = $query->paginate(OfferRedeemed::PAGE_SIZE);
			
			return $this->sendResponse ( $result );
	}
	
	
	public function purchases(Request $request) {
		
		$query = user::select('user.id', 'name', 'phone', 'user.email', 'gender', DB::raw('IF(expiry is null ,"unpaid", "paid") as status'), 'subscription.created_at as purchased_on')
		->join('subscription', 'user.id', '=','subscription.user_id')
		->where('subscription.subscription_type_id', '=', '1')
		->orderBy('subscription.created_at', 'desc');
		
		if(isset($_GET['after']))
			$query->where('subscription.created_at', '>', $_GET['after']);
			
			$result = $query->paginate(OfferRedeemed::PAGE_SIZE);
			
			return $this->sendResponse ( $result );
	}
	
	
	public function redeemptions(Request $request) {
		
		$query = OfferRedeemed::select('offer_redeemed.id as redeemed_id', 'offers_redeemed', 'offer_redeemed.created_at as redeemed_on', 'user_id', 'user.name as user_name', 'offer_id', 'offer.title', 'outlet_id', 'outlet.name as outlet_name', 'area' )
	 	->join('user', 'user.id', '=','offer_redeemed.user_id' )
	 	->join('offer', 'offer.id', '=','offer_redeemed.offer_id' )
	 	->join('outlet', 'outlet.id', '=','offer_redeemed.outlet_id' )
	 	->orderBy('offer_redeemed.created_at', 'desc');
		
		if(isset($_GET['after']))
			$query->where('offer_redeemed.created_at', '>', $_GET['after']);
	 	
	 	$result = $query->paginate(OfferRedeemed::PAGE_SIZE);
			
		return $this->sendResponse ( $result );
	}
	
}
?>