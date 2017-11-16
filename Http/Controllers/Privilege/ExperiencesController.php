<?php

namespace App\Http\Controllers\Privilege;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\Experiences;
use App\Models\Privilege\ExpData;
use App\Models\Privilege\ExpPurchasesOrder;
use App\Models\Privilege\ExpPurchases;
use App\Models\Privilege\ExperiencesSeats;

class ExperiencesController extends Controller {
	
	public function userHistory(Request $request) {
		
		$query = 'SELECT exp_id, e.title, latitude, longitude, e.address, o.id as order_id, o.total_tickets, o.non_veg, e.cost, o.convenience_fee, o.taxes, o.txn_amount,  payment_status, txn_id, start_time, end_time , p.created_at FROM `exp_purchases` p
		LEFT JOIN exp_purchases_order o on o.id = p.order_id
		INNER JOIN experiences e on e.id = o.exp_id
		WHERE p.user_id = '.$_SESSION['user_id'];
		
		if(isset($_GET['status']))
			$status = $_GET['status'];
			else
				$status = 'success';
				switch ($status){
					// 			case "success":
					// 				$query .= 'AND p.payment_status = "TXN_SUCCESS" ';
					// 			break;
					case "failure":
						$query .= ' AND p.payment_status = "TXN_FAILURE" ';
						break;
// 					case "pending":
// 						$query .= ' AND p.payment_status is null ';
// 						break;
					case "all":
						// 				$query .= 'AND p.payment_status = "TXN_FAILURE" ';
						break;
					default:
						$query .= ' AND p.payment_status = "TXN_SUCCESS" ';
				}
		
		$query .= ' order by o.created_at desc'; 
		$result = DB ::connection('ft_privilege')->select( DB::raw($query));
		return $this->sendResponse ( $result );
	}
	
	
	public function expUsers(Request $request, $id) {
		
		$query = 'SELECT exp.id as exp_id, exp.title, u.id as user_id, u.name, o.id as order_id, txn_id, IFNULL (p.payment_status, "PENDING") payment_status , total_tickets, non_veg, txn_amount FROM `exp_purchases_order` o
		LEFT JOIN exp_purchases p on p.order_id = o.id
		INNER JOIN user u on o.user_id = u.id
		INNER JOIN experiences exp on exp.id = o.exp_id
		WHERE exp.id = '.$id;
		
		if(isset($_GET['status']))
			$status = $_GET['status'];
		else
			$status = 'success';
		switch ($status){
// 			case "success":
// 				$query .= 'AND p.payment_status = "TXN_SUCCESS" '; 
// 			break;	
			case "failure":
				$query .= ' AND p.payment_status = "TXN_FAILURE" ';
				break;
			case "pending":
				$query .= ' AND p.payment_status is null ';
				break;
			case "all":
// 				$query .= 'AND p.payment_status = "TXN_FAILURE" ';
				break;
			default: 
				$query .= ' AND p.payment_status = "TXN_SUCCESS" ';
		}
		
		$result = DB ::connection('ft_privilege')->select( DB::raw($query));
		
	 	return $this->sendResponse ( $result );
	}
	
	
	public function estimateOrder(Request $request, $id) {
		
		$exp = Experiences::find ( $id );
		$result = array();
		$attributes =	$request->getRawPost(true);
		
		$avilableSeats = $exp->avilable_seats - $exp->seats($_SESSION['user_id'])->sum('blocked_seats');
		
		if($attributes['total_tickets'] > $avilableSeats){
			if($avilableSeats > 0)
				return $this->sendResponse ( 'ERROR! : Only '.$avilableSeats.' tickets are left! ',  self::NOT_ACCEPTABLE, 'OOPS! Only '.$avilableSeats.' tickets are left.');
			else
				return $this->sendResponse ( 'ERROR! : Sold Out!',  self::NOT_ACCEPTABLE, 'OOPS! Sold Out.');
		}
			
		
		$txn = $exp->estimateCost($attributes['total_tickets']);
		
		return $this->sendResponse ( $txn );
	}
	
	public function createOrder(Request $request, $id) {
		
		$exp = Experiences::find ( $id );
		$result = array();
		$attributes =	$request->getRawPost(true);
		
		$avilableSeats = $exp->avilable_seats - $exp->seats($_SESSION['user_id'])->sum('blocked_seats');
		
		if($attributes['total_tickets'] > $avilableSeats){
			if($avilableSeats > 0)
				return $this->sendResponse ( 'ERROR! : Only '.$avilableSeats.' tickets are left! ',  self::NOT_ACCEPTABLE, 'OOPS! Only '.$avilableSeats.' tickets are left.');
			else
				return $this->sendResponse ( 'ERROR! : Sold Out!',  self::NOT_ACCEPTABLE, 'OOPS! Sold Out.');
		}
		
		$txn = $exp->estimateCost($attributes['total_tickets']);
		
		if($exp){
			$result['MID'] = PAYTM_MERCHANT_MID;
			$result['CUST_ID'] = $_SESSION['user_id'];
			$result['INDUSTRY_TYPE_ID'] = PAYTM_INDUSTRY_TYPE_ID;
			$result['TXN_AMOUNT'] = (string)$txn->amount;
			$result['WEBSITE'] = PAYTM_MERCHANT_WEBSITE;
			
			if(isset($arr->source) and 'web' == strtolower($arr->source)){
				$result['CHANNEL_ID'] = 'WEB';
				$result['CALLBACK_URL'] = "http://api.foodtalk.in/paytm";
			}else{
				$result['CHANNEL_ID'] = 'WAP';
				$result['CALLBACK_URL'] = PAYTM_CALLBACK_URL;
				// 			"http://api.foodtalk.in/paytm";
			}
			
			$ORDER_ID = sha1($_SESSION['user_id'].'-'.microtime());
			$result['ORDER_ID'] = $ORDER_ID;
			
			$purchases_data['id']= $ORDER_ID;
			$purchases_data['exp_id'] = $exp->id;
			$purchases_data['user_id'] = $_SESSION['user_id'];
			$purchases_data['total_tickets'] = $attributes['total_tickets'];
			$purchases_data['non_veg'] = $attributes['non_veg'];
			$purchases_data['txn_amount'] = $txn->amount;
			$purchases_data['taxes'] = $txn->taxes;
			$purchases_data['convenience_fee'] = $txn->convenience_fee;
			$purchases_data['channel'] = $result['CHANNEL_ID'];
			
			$purchases_order = ExpPurchasesOrder ::create($purchases_data);
			
			require_once  __DIR__.'/../../../../public/encdec_paytm.php';
			// 		require '/var/www/html/lumen/app/public/encdec_paytm.php';
			$result['CHECKSUMHASH']= getChecksumFromArray($result ,PAYTM_MERCHANT_KEY);
			
			//Block Seats
			
// 			$blockArr['user_id'] = $_SESSION['user_id'];
			$blockArr['exp_id'] = $exp->id;
			$blockArr['order_id'] = $ORDER_ID;
			$blockArr['blocked_seats'] = $purchases_data['total_tickets'];
			
			$blockSeats = ExperiencesSeats::updateOrCreate( ['user_id'=>$_SESSION['user_id']], $blockArr);
			
		}
		return $this->sendResponse ( $result );
	}
	
	public function orderStatus(Request $request, $id) {
		
		$purchases_order = ExpPurchasesOrder::find( $id );
		
		if(!$purchases_order){
			return $this->sendResponse ( 'ERROR! : Invalid order_id',  self::NO_ENTITY, 'ERROR! : Invalid order_id');
		}
		
		$exp_purchases = ExpPurchases::where('order_id', '=', $id)->first();
		
		if(!$exp_purchases or $exp_purchases->payment_status!='TXN_SUCCESS'){
			
			require_once  __DIR__.'/../../../../public/encdec_paytm.php';
			$queryParam=array();
			$queryParam['MID'] = PAYTM_MERCHANT_MID;
			$queryParam['ORDERID'] = $id;
			$queryParam['CHECKSUMHASH']= getChecksumFromArray($queryParam,PAYTM_MERCHANT_KEY);
			
			$paytm_txn_order = file_get_contents(PAYTM_STATUS_QUERY_NEW_URL.'?JsonData='.urlencode(json_encode($queryParam)));
			
			$txn_order = json_decode($paytm_txn_order);
			
			$exp_purchases = ExpPurchases::firstOrCreate(['order_id' =>$id]);
			$exp_purchases->user_id = $purchases_order->user_id;
			$exp_purchases->payment_status = $txn_order->STATUS;
			$exp_purchases->txn_id= $txn_order->TXNID;
			$exp_purchases->metadata = $paytm_txn_order;
			$exp_purchases->save();
			
			$blockedSeats = ExperiencesSeats::where('order_id', $id)->first();
			if($blockedSeats)
				$blockedSeats->delete();
			
		}
		return $this->sendResponse ( $exp_purchases );
	}
	
	
	public function get(Request $request, $id, $with = false) {
		$exp = Experiences::find ( $id );
		if($exp){
			$exp->data;
			$exp->city;
		}
		
		return $this->sendResponse ( $exp);
	}
	
	public function getAll(Request $request) {
		
		$pageSize = Experiences::PAGE_SIZE;
		
		if (isset ($_SESSION ['admin_id'] ))
		{	
			$pageSize = 20;
			
			if(isset($_GET['is_active']))
				$where['is_active'] = $_GET['is_active'];
			
			if(isset($_GET['is_disabled']))
					$where['is_disabled'] = $_GET['is_disabled'];
			else
				$where['is_disabled'] = '0';
		}else {
			$where['is_active'] = '1';
			$where['is_disabled'] = '0';
		}
		
		$exp = Experiences::where( $where )->orderBy('created_at' ,'desc')->with('city')->paginate($pageSize);
		
		
		return $this->sendResponse ( $exp );
	}
	
	public function create(Request $request) {
		
		$attributes =	$request->getRawPost(true);
		unset($attributes['is_active']);
		unset($attributes['is_disabled']);
		$result = Experiences::create ( $attributes );
		
		return $this->sendResponse ( $result );
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		unset($attributes['is_active']);
		unset($attributes['is_disabled']);
		$exp = Experiences::find ( $id );
		$exp->update ( $attributes );
		
		return $this->sendResponse ( $exp);
	}

	public function delete($id) {
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 0;
			$exp->is_disabled = 1;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	public function activate($id){
		
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 1;
			$exp->is_disabled = 0;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences activated' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	public function deactivate($id){
		
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 0;
			$exp->is_disabled = 0;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences deactivated' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	
	public function addData(Request $request, $id) {
		
		$attributes =	$request->getRawPost(true);
		$attributes['exp_id'] = $id;
		$result = ExpData::create ( $attributes );
		
		return $this->sendResponse ( $result );
	}
	
	
	public function updateData(Request $request, $id) {
		
		$attributes =	$request->getRawPost(true);
		unset($attributes['exp_id']);
		$result = ExpData::find ( $id );
		if($result)
			$result->update ( $attributes );
			
		return $this->sendResponse ( $result );
	}
	
	public function deleteData(Request $request, $id) {
		
		$result = ExpData::find ( $id );
		if($result){
			$result->delete();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'deleted' );
		}
		return $this->sendResponse ( false );
	}
	
	
	public function sortData(Request $request, $id) {
		$attributes = $request->getRawPost(true);
		
		foreach ($attributes as $order => $data ){
			$exp_data = ExpData::where(['id'=>$data, 'exp_id'=>$id])->first();
			if($exp_data){
				$exp_data->sort_order = $order;
				$exp_data->save();
			}
		}
		return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'order updated' );
	}
	
}
?>