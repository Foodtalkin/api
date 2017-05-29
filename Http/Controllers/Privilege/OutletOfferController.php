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
use App\Models\Privilege\OutletOffer;

class OutletOfferController extends Controller {

	
	public function get(Request $request, $id) {
		$result = OutletOffer::find ( $id );
// 		$result['offer'] = $result->offer();
// 		$result->outlet();
		
		return $this->sendResponse ( $result);
	}
	
	
	public function listAll() {

		$result = OutletOffer::paginate(OutletOffer::PAGE_SIZE);
		return $this->sendResponse ( $result);
	}

	public function saveOutletOffer(Request $request, $id = false) {
		
		$post =	$request->getRawPost();
		
		try{
		
			if($id >0){
				$result = OutletOffer::find ( $id );
				if(isset($post->outlet_id))
				$result->outlet_id = $post->outlet_id;
				if(isset($post->offer_id))
				$result->offer_id = $post->offer_id;
			}
			else {
				$result = new OutletOffer();
				
				$result->outlet_id = $post->outlet_id;
				$result->offer_id = $post->offer_id;
			}
				

			
			if(isset($post->cover_image))
			$result->cover_image= $post->cover_image;
			
			if(isset($post->short_description))
			$result->short_description= $post->short_description;
			
			if(isset($post->description))
			$result->description= $post->description;
			
			if(isset($post->start_date))
			$result->start_date= $post->start_date;
			
			if(isset($post->end_date))
			$result->end_date= $post->end_date;
			if(isset($post->purchase_limit))
			$result->purchase_limit= $post->purchase_limit;
			if(isset($post->limit_per_purchase))
			$result->limit_per_purchase = $post->limit_per_purchase;

			if(isset($post->is_disabled))
				$result->is_disabled = $post->is_disabled;
			
			
			$result->save();

		} catch(\Illuminate\Database\QueryException $ex){
			
			// Note any method of class PDOException can be called on $ex.
			switch ($ex->errorInfo[1]){
				
				case 1452:
					return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'invalid offer or outlet ');
					break;	
				
				case 1062:
					return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'Duplicate entry');
					break;
				default:
					return $this->sendResponse ( false, self::NOT_ACCEPTABLE , $ex->errorInfo[2]);
			}
			
		}
// 			$result-> = $post->;
		return $this->sendResponse ( $result);
	}
	
	public function disable($id) {
		
		$result = OutletOffer::find ( $id );
		if($result){
			$result->is_disabled = 1;
			$result->save();
		}else 
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'no such outletoffer');
		return $this->sendResponse ( true );
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
	
	public function create(Request $request) {
		
		$attributes = $this->getResponseArr ( $request );
		if(!isset($attributes['facebook_id'])){
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'Invalid request, No facebook_id provided');
		}
		
		$user = User::where ( 'facebook_id', $attributes['facebook_id'] )->first ();
		if(!$user){
			
			if (isset($attributes['email'])){
				$user = User::where ( 'email', $attributes['email'] )->first ();
				if($user)
					unset($attributes['email']);
				}			
			
			$user = User::create ( $attributes );
			$user['is_new'] = true;

		}
		return $this->sendResponse ( $user );
	}
	
	public function update(Request $request, $id) {
		$user = User::find ( $id );
		$user->update ( $this->getResponseArr ( $request ) );
		
		return $this->sendResponse ( $user );
	}
	public function delete($id) {
		$user = User::find ( $id );
		
		if ($user) {
			$user->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	public function participation(Request $request, $id, $ptype) {

		$requestArr = $this->getResponseArr ( $request );
		$participant = array ();
		
		
		if ($ptype=='rsvp'){
			$ep  = EventParticipant::where(array('events_id'=>$requestArr ['events_id'], 'user_id' => $id))->first();
			
			if(!empty($ep)){
				return $this->sendResponse ( $ep, self::NOT_ACCEPTABLE, "You already participated" );
			}
		}else{
			$transaction_id = md5(time().'_'.$requestArr ['events_id'].'_'.$id) ;
			$participant ['transaction_id'] = $transaction_id;
		}
		
		$event = Events::find ( $requestArr ['events_id'] );		
		
		
		$participant ['subscribe'] = 0;
		
		if (isset ( $requestArr ['payment_id'] ))
			$participant ['payment_id'] = $requestArr ['payment_id'];
		
		if (isset ( $requestArr ['payment_method'] ))
			$participant ['payment_method'] = $requestArr ['payment_method'];
		
		if (isset ( $requestArr ['quantity'] ))
			$participant ['quantity'] = $requestArr ['quantity'];
		
		if (isset ( $requestArr ['payment'] ))
			$participant ['payment'] = $requestArr ['payment'];
		
		if (isset ( $requestArr ['email'] ))
			$participant ['email'] = $requestArr ['email'];
		
		if (isset ( $requestArr ['subscribe'] ) && $requestArr ['subscribe'] =='1' && isset ( $requestArr ['email'] ) && strlen($requestArr ['email']) > 2 ) {			
			$this->addToMailList(array('email'=>$requestArr ['email']), $event->location);
			$participant ['subscribe'] = 1;
		}
		
		if (isset ( $requestArr ['contact'] ))
			$participant ['contact'] = $requestArr ['contact'];
		
		if (isset ( $requestArr ['metadata'] ))
			$participant ['metadata'] = $requestArr ['metadata'];
		
		if (isset ( $requestArr ['response'] ))
			$participant ['response'] = json_encode ( $requestArr ['response'] );
		
		if (isset ( $requestArr ['source'] ))
			$participant ['source'] = json_encode ( $requestArr ['source'] );
		
// 		$result = User::find ( $id )->events ()->save ( $event, $participant );

		$user = User::find ( $id );
		if($user)
			$result = $user->events ()->save ( $event, $participant );
		else {
			$participant['events_id'] = $requestArr ['events_id'];
			$participant['user_id'] = $id;
			$result = EventParticipant::create($participant);
// 			$result = $user->events ()->save ( $event, $participant );
		}
		
		if(isset($transaction_id))
			$result['transaction_id'] = $transaction_id;
		
		return $this->sendResponse ( $result );
	}
}
?>