<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller {
	
	// list all user
	public function listAll() {
				
//     	DB::connection()->enableQueryLog();
		$Transaction = Transaction::orderBy('created_at', 'desc')
									->paginate ($this->pageSize);
// 		$que = DB::connection()->getQueryLog();
// 						        var_dump($que);
		return $this->sendResponse ($Transaction, self::SUCCESS_OK, 'Success');
	}
	
	// gets a user with id
	public function get(Request $request, $id, $with = false) {
		
		$partners = Partners::where ( array ('id' =>$id) )->first ();
		
		if ($partners && $with == 'events') 
			$partners ['events'] = $partners->events;
		
		return $this->sendResponse ( $partners );
	}
	public function create(Request $request, $method , $id = false) {
		
		$attributes = array();
		
		if($method == 'instamojo'){
				
			
			if(isset($_POST['custom_fields'])){
				
				$custom_fields = json_decode($_POST['custom_fields'], true);
				foreach ($custom_fields as $key => $field ){
					
					if(isset($field['label']) && $field['label'] == 'transaction_id' ){						
						$attributes['transaction_id'] = $field['value'];
					}
				}
			}
			if(is_numeric($id) && $id > 0){
				$attributes['event_id'] = $id;
			}
			
			
			if(isset($_POST['payment_id']))
				$attributes['payment_id'] = $_POST['payment_id'];
			
			if(isset($_POST['amount']))
				$attributes['amount'] = $_POST['amount'];
			
			if(isset($_POST['buyer']))
				$attributes['buyer_email'] = $_POST['buyer'];
			
			if(isset($_POST['buyer_name']))
				$attributes['buyer_name'] = $_POST['buyer_name'];

			$attributes['method'] = $method;
			$attributes['metadata'] = json_encode($_POST);
			
		}
		
		$Transaction = Transaction::create ($attributes);
		return $this->sendResponse ( $Transaction  );
	}
	public function update(Request $request, $id) {
		
		$partners = Partners::where ( array ('id' =>$id) )->first ();
		
		if ($partners) {
			$partners->update ( $this->getResponseArr ( $request ) );
			return $this->sendResponse ( $partners );
			
		} else {
			return $this->sendResponse ( null );
		}
		
		
	}
	public function delete($id) {
		
		$partners = Partners::where ( array ('id' =>$id) )->first ();
		if ($partners) {
			$partners->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
}
?>