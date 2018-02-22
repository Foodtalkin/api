<?php

namespace App\Http\Controllers\Privilege;

use App\Http\Controllers\Controller;
use App\Models\Privilege\DBLog;
use App\Models\Privilege\Experiences;
use App\Models\Privilege\ExpPurchasesOrder;
use App\Models\Privilege\Offer;
use App\Models\Privilege\OfferRedeemed;
use App\Models\Privilege\PaytmOrder;
use App\Models\Privilege\PaytmOrderStatus;
use App\Models\Privilege\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

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

        $sql = 'SELECT "subscription" as title, paytm_order_id as order_id, user_id, user.name, user.email, user.phone, txn_id, txn_amount, paytm_order_status.created_at, user.name, user.email, user.phone
		FROM `paytm_order_status`
		INNER JOIN paytm_order on paytm_order.id = `paytm_order_status`.`paytm_order_id` 
		INNER JOIN user on user.id = paytm_order.user_id  
		WHERE paytm_order_status.payment_status = "TXN_SUCCESS" '.$s1.'
		UNION
		SELECT experiences.title, order_id, exp_purchases_order.user_id, user.name, user.email, user.phone, txn_id, exp_purchases_order.txn_amount, exp_purchases.created_at, user.name, user.email, user.phone  
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


    public function purchases(Request $request)
    {
        $query = PaytmOrderStatus::select('user.id', 'user.name', 'user.phone', 'user.email', 'gender', DB::raw('IF(expiry is null ,"unpaid", "paid") as status'), DB::raw('IF(coupons.code is null, "NULL", coupons.code) as coupon'), 'subscription.created_at as purchased_on')
            ->join('paytm_order', 'paytm_order.id', '=', 'paytm_order_status.paytm_order_id')
            ->leftJoin('coupons', 'coupons.id', '=', 'paytm_order.coupon_id')
            ->join('user', 'user.id', '=', 'paytm_order.user_id')
            ->join('subscription', 'user.id', '=','subscription.user_id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('subscription.subscription_type_id', '=', '1')
            ->orderBy('subscription.created_at', 'desc');

        /*$query = user::select('user.id', 'name', 'phone', 'user.email', 'gender', DB::raw('IF(expiry is null ,"unpaid", "paid") as status'), 'subscription.created_at as purchased_on')
        ->join('subscription', 'user.id', '=','subscription.user_id')
        ->where('subscription.subscription_type_id', '=', '1')
        ->orderBy('subscription.created_at', 'desc');*/

        if (isset($_GET['after'])) {
            $query->where('subscription.created_at', '>', $_GET['after']);
        }

        return $this->sendResponse($query->paginate(OfferRedeemed::PAGE_SIZE));
    }

    public function eventPurchases(Request $request)
    {
        $query = User::select('user.id', 'name', 'phone', 'user.email', 'txn_amount', 'exp_purchases_order.created_at as purchased_on', 'exp_purchases.payment_status', 'experiences.title')
            ->join('exp_purchases_order', 'user.id', '=','exp_purchases_order.user_id')
            ->join('exp_purchases', 'exp_purchases.order_id', '=','exp_purchases_order.id')
            ->join('experiences', 'experiences.id', '=','exp_purchases_order.exp_id')
            ->where('exp_purchases.payment_status', 'TXN_SUCCESS')
            ->orderBy('exp_purchases_order.created_at', 'DESC');

        $result = $query->paginate(ExpPurchasesOrder::PAGE_SIZE);

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

        return $this->sendResponse($result);
    }

    public function salesRevenue()
    {
        $data = [];

        // DAILY
        $dates = $expSaleDates = $total = [];
        foreach (range(-6, 0) AS $i) {
            $date = Carbon::now()->addDays($i)
                ->format('Y-m-d');
            $dates[] = [
                'x' => $date,
                'y' => 0,
                'series' => 0,
            ];
            $expSaleDates[] = [
                'x' => $date,
                'y' => 0,
                'series' => 1
            ];
            $total[] = [
                'x' => $date,
                'y' => 0,
                'series' => 2,
            ];
            if ($i == -6) {
                $firstDate = $date;
            }
        }

        $subscriptions = PaytmOrderStatus::selectRaw('DATE(paytm_order_status.created_at) as date, SUM(paytm_order.txn_amount) as sales')
            ->join('paytm_order', 'paytm_order.id', '=', 'paytm_order_status.paytm_order_id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('paytm_order_status.created_at', '>=', $firstDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $subscriptions->map(function ($subscription) use (&$dates) {
            $key = array_search($subscription->date, array_column($dates, 'x'));
            $dates[$key]['y'] = (int)$subscription->sales;
        });

        $sales = ExpPurchasesOrder::join('exp_purchases', 'exp_purchases.order_id', '=', 'exp_purchases_order.id')
            ->where('exp_purchases_order.created_at', '>=', $firstDate)
            ->where('exp_purchases.payment_status', 'TXN_SUCCESS')
            ->groupBy('date')
            ->get([
                DB::raw('DATE(exp_purchases_order.created_at) as date'),
                DB::raw('SUM(txn_amount) as sales')
            ]);
        $sales->map(function ($sale) use (&$expSaleDates) {
            $key = array_search($sale->date, array_column($expSaleDates, 'x'));
            $expSaleDates[$key]['y'] = (int)$sale->sales;
        });
        foreach ($expSaleDates as $key => $expSaleDate) {
            $total[$key]['y'] = $expSaleDate['y'] + $dates[$key]['y'];
        }
        $data['daily'] = [
            [
                'key' => 'Subscription Sales',
                'values' => $dates
            ],
            [
                'key' => 'Experience Sales',
                'values' => $expSaleDates
            ],
            [
                'key' => 'Total',
                'values' => $total
            ]
        ];

        // WEEKLY
        $dates = $expSaleDates = $total = [];
        $startDate = null;
        $endDate = null;
        foreach (range(-3, 0) as $i) {
            $date = Carbon::now()->addWeek($i);
            $dates[] = [
                'x' => $date->startOfWeek()->toDateString() . ',' . $date->endOfWeek()->toDateString(),
                'y' => 0,
                'series' => 0,
                'week' => $date->weekOfYear
            ];
            $expSaleDates[] = [
                'x' => $date->startOfWeek()->toDateString() . ',' . $date->endOfWeek()->toDateString(),
                'y' => 0,
                'series' => 1,
                'week' => $date->weekOfYear
            ];
            $total[] = [
                'x' => $date->startOfWeek()->toDateString() . ',' . $date->endOfWeek()->toDateString(),
                'y' => 0,
                'series' => 2,
                'week' => $date->weekOfYear
            ];
            if ($i == -3) {
                $startDate = $date->startOfWeek()->toDateString() . ' 00:00:00';
            }
            if ($i == 0) {
                $endDate = $date->endOfWeek()->toDateString() . ' 23:59:59';
            }
        }

        $subscriptions = PaytmOrderStatus::selectRaw('SUM(paytm_order.txn_amount) as total, paytm_order_status.created_at, WEEKOFYEAR(paytm_order_status.created_at) as week')
            ->join('paytm_order', 'paytm_order.id', '=', 'paytm_order_status.paytm_order_id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('paytm_order_status.created_at', '>=', $startDate)
            ->where('paytm_order_status.created_at', '<=', $endDate)
            ->groupBy('week')
            ->take(4)
            ->get();

        $subscriptions->each(function ($subscription) use (&$dates) {
            $key = array_search($subscription->week, array_column($dates, 'week'));
            $dates[$key]['y'] = (int)$subscription->total;
        });

        $sales = ExpPurchasesOrder::join('exp_purchases', 'exp_purchases.order_id', '=', 'exp_purchases_order.id')
            ->where('exp_purchases_order.created_at', '>=', $startDate)
            ->where('exp_purchases_order.created_at', '<=', $endDate)
            ->where('exp_purchases.payment_status', 'TXN_SUCCESS')
            ->groupBy('week')
            ->get([
                DB::raw('WEEKOFYEAR(exp_purchases_order.created_at) as week'),
                DB::raw('SUM(txn_amount) as sales')
            ]);
        $sales->map(function ($sale) use (&$expSaleDates) {
            $key = array_search($sale->week, array_column($expSaleDates, 'week'));
            $expSaleDates[$key]['y'] = (int)$sale->sales;
        });
        foreach ($expSaleDates as $key => $expSaleDate) {
            $total[$key]['y'] = $expSaleDate['y'] + $dates[$key]['y'];
        }

        $data['weekly'] = [
            [
                'key' => 'Subscription Sales',
                'values' => $dates
            ],
            [
                'key' => 'Experience Sales',
                'values' => $expSaleDates
            ],
            [
                'key' => 'Total',
                'values' => $total
            ]
        ];

        // MONTHLY
        $dates = $expSaleDates = $total = [];
        $startDate = null;
        $endDate = null;
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonth($i);
            $dates[] = [
                'x' => $date->startOfMonth()->toDateString() . ',' . $date->endOfMonth()->toDateString(),
                'y' => 0,
                'series' => 0,
                'month' => $date->month
            ];
            $expSaleDates[] = [
                'x' => $date->startOfMonth()->toDateString() . ',' . $date->endOfMonth()->toDateString(),
                'y' => 0,
                'series' => 1,
                'month' => $date->month
            ];
            $total[] = [
                'x' => $date->startOfMonth()->toDateString() . ',' . $date->endOfMonth()->toDateString(),
                'y' => 0,
                'series' => 2,
                'month' => $date->month
            ];
            if ($i == -11) {
                $startDate = $date->startOfMonth()->toDateString() . ' 00:00:00';
            }
            if ($i == 0) {
                $endDate = $date->endOfMonth()->toDateString() . ' 23:59:59';
            }
        }

        $subscriptions = PaytmOrderStatus::selectRaw('SUM(paytm_order.txn_amount) as total, paytm_order_status.created_at, MONTH(paytm_order_status.created_at) as month')
            ->join('paytm_order', 'paytm_order.id', '=', 'paytm_order_status.paytm_order_id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('paytm_order_status.created_at', '>=', $startDate)
            ->where('paytm_order_status.created_at', '<=', $endDate)
            ->groupBy('month')
            ->take(4)
            ->get();

        $subscriptions->map(function ($subscription) use (&$dates) {
            $key = array_search($subscription->month, array_column($dates, 'month'));
            $dates[$key]['y'] = (int)$subscription->total;
        });

        $sales = ExpPurchasesOrder::join('exp_purchases', 'exp_purchases.order_id', '=', 'exp_purchases_order.id')
            ->where('exp_purchases.payment_status', 'TXN_SUCCESS')
            ->where('exp_purchases_order.created_at', '>=', $startDate)
            ->where('exp_purchases_order.created_at', '<=', $endDate)
            ->groupBy('month')
            ->get([
                DB::raw('MONTH(exp_purchases_order.created_at) as month'),
                DB::raw('SUM(txn_amount) as sales')
            ]);
        $sales->map(function ($sale) use (&$expSaleDates) {
            $key = array_search($sale->month, array_column($expSaleDates, 'month'));
            $expSaleDates[$key]['y'] = (int)$sale->sales;
        });
        foreach ($expSaleDates as $key => $expSaleDate) {
            $total[$key]['y'] = $expSaleDate['y'] + $dates[$key]['y'];
        }

        $data['monthly'] = [
            [
                'key' => 'Subscription Sales',
                'values' => $dates
            ],
            [
                'key' => 'Experience Sales',
                'values' => $expSaleDates
            ],
            [
                'key' => 'Total',
                'values' => $total
            ]
        ];

        return $data;
    }

    public function liveEvents()
    {
        $events = Experiences::selectRaw('title, avilable_seats,(total_seats - avilable_seats) as booked_seats')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function valuableUsers()
    {
        $sql = "select user_id as user_id, user_name, sum(txn_amount) as total 
	from ( 

		SELECT user_id, user.name as user_name, txn_amount, paytm_order_status.created_at 
		FROM 
			`paytm_order_status` 

		INNER JOIN paytm_order on paytm_order.id = `paytm_order_status`.`paytm_order_id` 
		INNER JOIN user on user.id = paytm_order.user_id WHERE paytm_order_status.payment_status = \"TXN_SUCCESS\" 

		UNION 

		SELECT exp_purchases_order.user_id, user.name as user_name, exp_purchases_order.txn_amount, exp_purchases.created_at 
		FROM 
			`exp_purchases` 
		INNER JOIN exp_purchases_order on exp_purchases.order_id = exp_purchases_order.id 
		INNER JOIN user on user.id = exp_purchases_order.user_id 
		INNER JOIN experiences on exp_purchases_order.exp_id = experiences.id WHERE exp_purchases.payment_status = \"TXN_SUCCESS\" ) 
	x  group by user_id ORDER by total DESC limit 10";

        return response()->json([
            'success' => true,
            'data' => DB::connection('ft_privilege')->select( DB::raw($sql) )
        ]);
    }

    public function onboardedUsersCount()
    {
        $usersWithoutCode = PaytmOrder::selectRaw('DISTINCT paytm_order.user_id')
            ->join('paytm_order_status', 'paytm_order_status.paytm_order_id', '=', 'paytm_order.id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('paytm_order.coupon_id', null)
            ->orderBy('paytm_order.created_at', 'DESC')
            ->distinct('paytm_order.user_id')
            ->count();


        $usersWithCode = PaytmOrder::selectRaw('DISTINCT paytm_order.user_id')
            ->join('paytm_order_status', 'paytm_order_status.paytm_order_id', '=', 'paytm_order.id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('paytm_order.coupon_id', '!=', '')
            ->orderBy('paytm_order.created_at', 'DESC')
            //->distinct('paytm_order.user_id')
            ->count();

        return response()->json([
            'success' => true,
            'without_coupon_code' => $usersWithoutCode,
            'with_coupon_code' => $usersWithCode
        ]);
    }
}
?>