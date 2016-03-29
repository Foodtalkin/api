<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\SubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubCategoryController extends Controller {
	
	// list all user
	public function listAll() {
				
		$User = SubCategory::where ( 'is_disabled', '0' )->paginate ($this->pageSize);
		
		return $this->sendResponse ( response ()->json ( $User )->getData ( true ) );
	}
	
	// gets a user with id
	public function get(Request $request, $id, $with = false) {
		
		$cats = SubCategory::find ( $id );
		
		if ($cats && $with == 'events') {
			$cats ['events'] = $cats->events;
		}
		
		return $this->sendResponse ( $cats );
	}
	public function create(Request $request) {
		$cats = SubCategory::create ( $this->getResponseArr ( $request ) );
		return $this->sendResponse ( $cats );
	}
	public function update(Request $request, $id) {
		$cats = SubCategory::find ( $id );
		
		if ($cats) {
			$cats->update ( $this->getResponseArr ( $request ) );
			return $this->sendResponse ( $cats );
			
		} else {
			return $this->sendResponse ( null );
		}
		
		
	}
	public function delete($id) {
		$cats = SubCategory::find ( $id );
		
		if ($cats) {
			$cats->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
}
?>