<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\Partners;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TemplateController extends Controller {
	
	// list all user
	public function listAll() {
				
//     	DB::connection()->enableQueryLog();
		$partners = Partners::where ('is_disabled', '=', '0')
// 									->get();
									->paginate ($this->pageSize);
// 		$que = DB::connection()->getQueryLog();
// 						        var_dump($que);
		return $this->sendResponse ($partners, self::SUCCESS_OK, 'Success');
	}
	
	// gets a user with id
	public function get(Request $request, $id, $with = false) {
		
		$partners = Partners::where ( array ('id' =>$id) )->first ();
		
		if ($partners && $with == 'events') 
			$partners ['events'] = $partners->events;
		
		return $this->sendResponse ( $partners );
	}
	public function create(Request $request) {
		
		$attributes = $this->getResponseArr ( $request ) ;
// 		$attributes['type'] = $type;
		
		$partners = Partners::create ($attributes);
		return $this->sendResponse ( $partners  );
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