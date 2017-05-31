<?php

namespace App\Http\Controllers\Privilege;

// use DB;

use App\Models\Privilege\Outlet;
use App\Models\User;
use App\Models\Events;
use App\Models\Contest;
use App\Models\EventParticipant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OutletController extends Controller {

	
	// gets a user with id
	public function get(Request $request, $id, $with = false) {
		$Outlet = Outlet::find ( $id );
		$Outlet->offer;
		$Outlet->resturant;
		
		return $this->sendResponse ( $Outlet);
	}
	
	public function getAll(Request $request) {
		$Outlet= Outlet::all()
		// 		->paginate(Offer::PAGE_SIZE)
		;
		
		return $this->sendResponse ( $Outlet);
	}
	
	public function create(Request $request) {
		
		$attributes =	$request->getRawPost(true);
		$Outlet= Outlet::create ( $attributes );
		
		return $this->sendResponse ( $Outlet);
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		
		$Outlet = Outlet::find ( $id );
		$Outlet->update ( $attributes );
		
		return $this->sendResponse ( $Outlet );
	}
	
	public function delete($id) {
		$Outlet= Outlet::find ( $id );
		
		if ($Outlet) {
			$Outlet->is_disabled = 1;
			$Outlet->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Offer Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	
	// list all user
	public function listAll($for = 'all') {

		
		if($for=='nonapp'){
			
			$User = User::where ( 'is_disabled', '0' )->with('score')
			->leftjoin('activity_score', 'user.facebook_id', '=', 'activity_score.facebookId')
			->where ( 'activity_score.facebookId', null )
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
			
		}
		
			
		if($for=='onapp'){

			$User = User::where ( 'is_disabled', '0' )->with('score')
			->join('activity_score', 'user.facebook_id', '=', 'activity_score.facebookId')
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
				
		}
		
		if($for=='all'){
			$User = User::where ( 'is_disabled', '0' )->with('score')
			->orderBy('id', 'desc')->paginate ( $this->pageSize );
		}

		
		
		
// 		$User = User::where ( 'is_disabled', '0' )->with('score')		
// 		->orderBy('id', 'desc')->paginate ( $this->pageSize );
		
		
		return $this->sendResponse ( $User );
	}
	
	
	public function listAllWithCity($city = null) {
		
		$User = User::where ( 'is_disabled', '0' )->with('score')->where( 'city_id', $city )->orderBy('id', 'desc')->paginate ( $this->pageSize );		
		return $this->sendResponse ( $User );
	}
	
	
	
	
	public function tag($tags){

			$tags = urldecode($tags);
			$tags = explode(',', $tags);
			
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->join('event_participant', 'user.id', '=', 'event_participant.user_id')
			->join('events', 'events.id', '=', 'event_participant.events_id')
			->join('tags', 'events.id', '=', 'tags.events_id')
			->where(
					function($query) use ($tags){
						$first = true;
						foreach ($tags as $tag){
							if($first){
								$query->where ( 'tag_name', 'LIKE' , $tag);
								$first = false;
							}
							else
								$query->orwhere ( 'tag_name', 'LIKE' , $tag );
						}
					}
		
			)
			->groupBy('user.id')
// 			->orderBy('user.id', 'desc')
			->get();
// 			->paginate ( $this->pageSize );
			return $this->sendResponse ( $User );
		
	}
	
	public function search($text, $tags = null) {
		$text = urldecode($text);
		
		if(!is_null($tags)){
			$tags = urldecode($tags);
			$tags = explode(',', $tags);
			
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->where(
				function($query) use ($text){
					$query->where ( 'user.email', 'LIKE' , "%$text%") 
						  ->orwhere ( 'user.name', 'LIKE' , "%$text%" );
				}
			)
			->join('event_participant', 'user.id', '=', 'event_participant.user_id')
			->join('events', 'events.id', '=', 'event_participant.events_id')
			->join('tags', 'events.id', '=', 'tags.events_id')
				->where(
						function($query) use ($tags){
							$first = true;
							foreach ($tags as $tag){
								if($first){
									$query->where ( 'tag_name', 'LIKE' , $tag);
									$first = false;
								}
								else 
									$query->orwhere ( 'tag_name', 'LIKE' , $tag );
							}
						}
						
// 						'tag_name',$tag
						)
			->groupBy('user.id')
			->orderBy('user.id', 'desc')->paginate ( $this->pageSize );
		}
		else {
			$User = User::select('user.*')->with('score')-> where ('user.is_disabled','0')
			->where(
					function($query) use ($text){
						$query->where ( 'user.email', 'LIKE' , "%$text%")
						->orwhere ( 'user.name', 'LIKE' , "%$text%" );
					}
			)
			->orderBy('user.id', 'desc')->paginate ( $this->pageSize );
		}
		return $this->sendResponse ( $User );
	}
	
	
	public function checkEmail(Request $request) {
		$attributes = $this->getResponseArr ( $request );
		$user = User::where ( 'email', $attributes['email'] )->first ();
		if($user){
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'This email is not avilable');
		} 
		return $this->sendResponse ( true, self::SUCCESS_OK, 'Email is avilable');
			
		
	}

}
?>