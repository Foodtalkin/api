<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\Vendors;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorsController extends Controller {
	
	// list all user
	public function listAll($type) {
				
		
		$Vendors = Vendors::with('category')->get()->where('type', $type);
		
// 		$Vendors = Vendors::where (array ('is_disabled' => 0, 'type' => $type))
		
// 		->paginate ($this->pageSize);
		
		return $this->sendResponse ($Vendors);
	}
	
	// gets a user with id
	public function get(Request $request, $id,$type, $with = false) {
		
		$Vendors = Vendors::where ( array ('id' =>$id, 'type' => $type) )->first ();
		$Vendors['category'] = $Vendors->category;
		
		if ($Vendors && $with == 'events') {
			$Vendors ['events'] = $Vendors->events;
		}
		
		return $this->sendResponse ( $Vendors );
	}
	public function create(Request $request, $type) {
		
		$attributes = $this->getResponseArr ( $request ) ;
		$attributes['type'] = $type;
		
		$Vendors = Vendors::create ($attributes);
		return $this->sendResponse ( $Vendors );
	}
	public function update(Request $request, $id, $type) {
		
		$Vendors = Vendors::where ( array ('id' =>$id, 'type' => $type) )->first ();
		
		if ($Vendors) {
			$Vendors->update ( $this->getResponseArr ( $request ) );
			return $this->sendResponse ( $Vendors );
			
		} else {
			return $this->sendResponse ( null );
		}
		
		
	}
	public function delete($id, $type) {
		
		$Vendors = Vendors::where ( array ('id' =>$id, 'type' => $type) )->first ();
		if ($Vendors) {
			$Vendors->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
}
?>