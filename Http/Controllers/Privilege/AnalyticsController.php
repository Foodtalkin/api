<?php

namespace App\Http\Controllers\Privilege;

use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\DBLog;
use App\Models\Privilege\User;
use App\Models\Privilege\Offer;
// use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller {

	
	
	public function users(Request $request ,$days = 7){
		
		
		$overall = DB::connection('ft_privilege')->select( DB::raw('select IF( subscription.id is null, "unpaid", "paid" ) as status, count(1) as count  from user LEFT JOIN subscription on subscription.user_id = user.id  GROUP by status'));
		
// 		$lastdays = DB::connection('ft_privilege')->select( DB::raw('select IF( subscription.id is null, "unpaid", "paid" ) as status, count(1) as count  from user LEFT JOIN subscription on subscription.user_id = user.id where user.created_at >= DATE(NOW()) - INTERVAL '.$days.' DAY GROUP by status'));
		
		DB::connection('ft_privilege')->statement('SET @i=0');
		$total = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(created_at,"%Y-%m-%d") as onbord from user GROUP by onbord) as u on u.onbord = calander.theday'));

		DB::connection('ft_privilege')->statement('SET @i=0');
		$paid = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(subscription.created_at,"%Y-%m-%d") as onbord from user LEFT JOIN subscription on subscription.user_id = user.id WHERE subscription.id is not null GROUP by onbord ) as u on u.onbord = calander.theday'));

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
						'unpaid'=>$unpaid,
				)
		);
		return $this->sendResponse ( $result );
	}
	
	public function offers(Request $request ,$days = 7){
		
// 		$overall = DB::connection('ft_privilege')->select( DB::raw(''));

		$overall = DB::connection('ft_privilege')->select( DB::raw('SELECT count(offer_id) count, offer.title FROM offer left JOIN `offer_redeemed` on offer.id = offer_id group by offer.id'));
		
		$result = array(
				'overall'=>$overall,
				// 				'latest' => $lastdays,
// 				'datewise'=>array(
// 						'total'=>$total,
// 						'paid'=>$paid,
// 						'unpaid'=>$unpaid,
// 				)
		);
		
		$offers = Offer::where('is_disabled', '0')->get();
		
		foreach ($offers as $offer){
			DB::connection('ft_privilege')->statement('SET @i=0');
			$result['datewise'][$offer->title] = DB::connection('ft_privilege')->select( DB::raw('SELECT theday as date, IFNULL(cnt, 0 ) as count FROM (SELECT DATE(ADDDATE(DATE_SUB(NOW(), INTERVAL '.$days.' DAY), INTERVAL @i:=@i+1 DAY)) AS theday FROM cuisine HAVING @i < DATEDIFF(now(), DATE_SUB(NOW(), INTERVAL '.$days.' DAY))) as calander left join (select count(1) cnt, DATE_FORMAT(offer_redeemed.created_at,"%Y-%m-%d") as redeemed_on from offer_redeemed left JOIN `offer` on offer.id = offer_id WHERE offer.id = '.$offer->id.' GROUP by redeemed_on) as o on o.redeemed_on = calander.theday'));
		}
		
// 		$result['datewise'][$offer->title] = DB::connection('ft_privilege')->select( DB::raw(''));
		
		
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

}
?>