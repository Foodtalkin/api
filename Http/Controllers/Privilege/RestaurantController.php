<?php

namespace App\Http\Controllers\Privilege;

// use DB;

use App\Models\User;
use App\Models\Events;
// use App\Models\Contest;
use App\Models\EventParticipant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;

use DB;
use App\Models\Privilege\Restaurant;
use App\Models\Privilege\Offer;
use App\Models\Privilege\Outlet;
use App\Models\Privilege\Cuisine;

class RestaurantController extends Controller {

	
	public function listresto (Request $request) {
		$result = Restaurant::all();
// 		paginate(Restaurant::PAGE_SIZE);
		return $this->sendResponse ( $result );
	}
	
	
	public function get(Request $request, $id) {
		$result = Restaurant::find ( $id );
		$result->cuisine;
		$result->outlet;
		
		return $this->sendResponse ( $result );
	}
	
	public function create(Request $request) {
		
		$attributes =	$request->getRawPost(true);
		$restaurant = Restaurant::create ( $attributes );
			
		return $this->sendResponse ( $restaurant);
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		
		$restaurant= Restaurant::find ( $id );
		$restaurant->update ( $attributes );
		
		return $this->sendResponse ( $restaurant);
	}
	
	public function delete($id) {
		$restaurant= Restaurant::find ( $id );
		
		if ($restaurant) {
			$restaurant->is_disabled = 1;
			$restaurant->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Restaurant Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	
	
	public function listAll(Request $request) {
		
		$result = Offer::getAllOffers();	

		return $this->sendResponse ( $result );
	}
	
	public function cuisine(Request $request){
		
		$result = Cuisine::select(DB::raw('distinct  cuisine.id, cuisine.title'))
		->join('restaurant_cuisine', 'cuisine.id','=','restaurant_cuisine.cuisine_id')
		->join('restaurant', 'restaurant.id','=','restaurant_cuisine.restaurant_id')
		->where('restaurant.is_disabled', '=', '0')
		->orderBy('cuisine.title', 'asc')
		->get();
		
		return $this->sendResponse ( $result );
	}
	
	
	public function outlets($restaurant_id) {
		
		$result = Outlet::select(DB::raw('count(1) as offer_count, GROUP_CONCAT(DISTINCT offer.id) as offer_ids,  outlet.id,outlet.name,outlet.city_id, outlet.name, city_zone_id, address, postcode, outlet.work_hours'))
		->join('outlet_offer', 'outlet.id', '=', 'outlet_offer.outlet_id')
		->join('offer', 'offer.id','=','outlet_offer.offer_id')
		->where( 'offer.is_disabled', '0' )
		->where( 'outlet.is_disabled', '0' )
		->where( 'offer.is_active', '1' )
		->where('outlet.resturant_id',  $restaurant_id)
		->groupBy('outlet.id')
		->paginate(Outlet::PAGE_SIZE);
		
		return $this->sendResponse ( $result );
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