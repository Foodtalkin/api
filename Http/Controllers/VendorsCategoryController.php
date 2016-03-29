<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\SubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VendorsCategory;

class VendorsCategoryController extends Controller {
	
	// list all 
	public function listAll($type) {
				
		$cats = VendorsCategory::where (array ('is_disabled' => 0, 'type' => $type))->get();
// 		->paginate ($this->pageSize)
		
		return $this->sendResponse ( $cats );
	}
	
	// gets  with id
	public function get(Request $request, $id, $type, $with = false) {
		
		
		$cats = VendorsCategory::where ( array ('id' =>$id, 'type' => $type) )->first ();
		
		if ($cats && $with == 'vendors') {
			$cats ['vendors'] = $cats->Vendors;
		}
		
		return $this->sendResponse ( $cats );
	}
	public function create(Request $request, $type) {
		
		$attributes = $this->getResponseArr ( $request ) ;
		$attributes['type'] = $type;
		
		$cats = VendorsCategory::create ( $attributes );
		return $this->sendResponse ( $cats );
	}
	public function update(Request $request, $id, $type) {
		$cats = VendorsCategory::where ( array ('id' =>$id, 'type' => $type) )->first ();
		
		if ($cats) {
			$cats->update ( $this->getResponseArr ( $request ) );
			return $this->sendResponse ( $cats );
			
		} else {
			return $this->sendResponse ( null );
		}
		
		
	}
	public function delete($id, $type) {
		$cats = VendorsCategory::where ( array ('id' =>$id, 'type' => $type) )->first ();
		
		if ($cats) {
			$cats->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
}
?>