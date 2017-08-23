<?php

namespace App\Http\Controllers\Privilege;

// use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\PushNotification;
// use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller {

	public function getAll(Request $request) {
		$result = PushNotification::all();
		return $this->sendResponse ( $result);
	}
	
	public function get(Request $request, $id) {
		$result = PushNotification::find ( $id );
		return $this->sendResponse ( $result);
	}
	
	public function create(Request $request) {
		$attributes = $request->getRawPost(true);
		$attributes['push'] = json_encode($attributes['push']);
		
		if(isset($attributes['status']))
			unset($attributes['status']);
		
		$result = PushNotification::create ( $attributes );
		return $this->sendResponse ( $result);
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		$result= PushNotification::find ( $id );
		$attributes['push'] = json_encode($attributes['push']);
		
		if(isset($attributes['status']))
			unset($attributes['status']);
			
		$result->update ( $attributes );
		return $this->sendResponse ( $result);			
	}
	
	
	
	public function delete($id) {
		$result= PushNotification::find ( $id );
		
		if ($result) {
			$result->is_disabled = 1;
			$result->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Notification Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
}
?>