<?php

namespace App\Http\Controllers\Privilege;

use DB;

use App\Models\Privilege\Outlet;
use App\Models\Privilege\OfferRedeemed;
use App\Models\Privilege\Image;
// use App\Models\Privilege\RestaurantCuisine;
use App\Models\Privilege\Cuisine;
use App\Models\Privilege\Offer;
use App\Models\Privilege\User;
// use App\Models\User;
use App\Models\Events;
// use App\Models\Contest;
use App\Models\EventParticipant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\OutletOffer;
use App\Models\Privilege\Bookmark;
use App\Models\Privilege\ES;
use App\Models\Privilege\Sendgrid;

class OfferController extends Controller {

	
	public function get(Request $request, $id) {
		$offer = Offer::find ( $id );
// 		$offer->
		
		
		return $this->sendResponse ( $offer );
	}
	
	public function getAll(Request $request) {
		$offer = Offer::all()
// 		->paginate(Offer::PAGE_SIZE)
		;
		
		return $this->sendResponse ( $offer );
	}
	
	public function create(Request $request) {
		
		$attributes =	$request->getRawPost(true);
		$Offer= Offer::create ( $attributes );
		
		return $this->sendResponse ( $Offer);
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		
		$Offer= Offer::find ( $id );
		$Offer->update ( $attributes );
		
		return $this->sendResponse ( $Offer);
	}
	
	public function delete($id) {
		$restaurant= Offer::find ( $id );
		
		if ($restaurant) {
			$restaurant->is_disabled = 1;
			$restaurant->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Offer Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	

	public function redeemHistory(Request $request){
		$result = OfferRedeemed::select('outlet.name', 'offer_redeemed.id', 'offer_redeemed.offers_redeemed', 'offer_redeemed.created_at')
		->join('outlet', 'outlet.id', '=','offer_redeemed.outlet_id' )
		->where(array(
				'user_id' => $_SESSION['user_id']
		))
		->orderBy('offer_redeemed.created_at', 'desc')
		->get();
		return $this->sendResponse ( $result , self::SUCCESS_OK_NO_CONTENT);
	}
	
	public function redeem(Request $request) {
		
		$post =	$request->getRawPost();
		
		$outlet = Outlet::where(array(
				'id' => $post->outlet_id,
				'pin' => $post->pin,
				'is_disabled' => 0
		))->first();
		
		if(!$outlet){
			return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'ERROR! : invalid PIN');
		}
		
		$outletOffer = OutletOffer::where(array(
					'outlet_id' => $post->outlet_id,
					'offer_id' => $post->offer_id,
			))
// 			->where('start_date','<', DB::raw('now()'))
// 			->where('end_date','>', DB::raw('now()'))
			->first();
			
			
// 			echo 'offers_redeemed : '.$post->offers_redeemed;
// 			echo '<br> cost : '.$outlet->resturant->cost;
			
			if($outlet->resturant->cost < 500){
				$unitSaving = $outletOffer->offer->saving_budget;
			}elseif ($outlet->resturant->cost > 999){
				$unitSaving = $outletOffer->offer->saving_splurge;
			}else {
				$unitSaving = $outletOffer->offer->saving_mid;
			}
			
			
// 			echo '<br> unitSaving :  '.$unitSaving;
			
			
// 			var_dump($outletOffer);
// 			die('<br>DEAD');
			
			$redeemedHistory = OfferRedeemed::select(DB::raw('SUM(offers_redeemed) as total_offers_redeemed'))
			->where(array(
					'outlet_id' => $post->outlet_id,
					'offer_id' => $post->offer_id,
					'user_id' => $_SESSION['user_id']
			))->first();
			
			if(isset($outletOffer->limit_per_purchase) && $outletOffer->limit_per_purchase > 0){
// 				echo 'limit_per_purchase : +ve ';
				$couponLeft =  $outletOffer->limit_per_purchase - $redeemedHistory->total_offers_redeemed;
			}else{
// 				echo 'limit_per_purchase : -ve ';
				$couponLeft =  $outletOffer->purchase_limit - $redeemedHistory->total_offers_redeemed;
			}
				
			if($outletOffer){
				
				if($couponLeft < $post->offers_redeemed){
					return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'ERROR! : no / insufficient coupons left for you.');
				}
				
				$offerRedeem = new  OfferRedeemed();
				$offerRedeem->outlet_id = $post->outlet_id;
				$offerRedeem->offer_id = $post->offer_id;
				$offerRedeem->offers_redeemed = $post->offers_redeemed;
				$offerRedeem->user_id = $_SESSION['user_id'];
				$offerRedeem->saving = $unitSaving * $post->offers_redeemed;
				$offerRedeem->save();
			}else {
				return $this->sendResponse ( false, self::NOT_ACCEPTABLE , 'ERROR! : no such offer');
			}
	
			$option['restaurant_name'] = $outlet->name;
			$option['area'] = $outlet->area;
			$option['redeem_id'] = $offerRedeem->id;
			$option['offer'] = $outletOffer->offer->title;
			$option['coupon_count'] = $offerRedeem->offers_redeemed;
			$option['date'] = date_format($offerRedeem->created_at, 'D M Y');
			$option['time'] = date_format($offerRedeem->created_at, 'h:i A');;
			$body =  Sendgrid::redumption_tpl($option);

			if(PAYTM_ENVIRONMENT != 'TEST')
				$sendgridresponse =	Sendgrid::sendMail(explode(',', $outlet->email), 'Food Talk Redemption Confirmation', $body);
			
		return $this->sendResponse ( $offerRedeem );
	}

// 	public function redeemHistory(Request $request) {
		
// 		$user = User::find($_SESSION['user_id']);
		
// 		return $this->sendResponse ( $offerRedeem );
// 	}
	
	
	public function offerWithOutlet($outlet_id, $offer_id) {
		
		$result = Offer::getOfferWithOutlet($outlet_id, $offer_id);
		
		
		if($result){
			$result['images'] = Image::select(
					DB::raw('REPLACE(url,"/upload/","/upload/f_auto,w_500,q_70/") as url'),
					'title', 
					'type'
					)->where(array(
					'entity'=>'outlet',
					'entity_id'=>$outlet_id,
					'is_disabled'=>'0',
			))->get();
			
			$result['cuisine'] = Cuisine::select('title')
			->join('restaurant_cuisine', 'cuisine.id', '=', 'restaurant_cuisine.cuisine_id')
			->where('restaurant_cuisine.restaurant_id',$result->restaurant_id)
			->get();
		}
		
		
		return $this->sendResponse ( $result );
	}
	
	public function bookmark(Request $request, $id) {
		
		$result = Bookmark::firstOrNew( array('outlet_offer_id'=>$id, 'user_id'=>$_SESSION['user_id']) );
		$result->save();
		return $this->sendResponse ( $result );
	}
	
	public function removeBookmark(Request $request, $id) {
		
		$result = Bookmark::where(array('outlet_offer_id'=>$id, 'user_id'=>$_SESSION['user_id']))->first();
		if($result)
			$result->delete();
		
		return $this->sendResponse ( true );
		
	}
	
	
	public function listBookmark(Request $request) {
		
		$result = Bookmark::select('outlet.name', 'outlet.area', 'bookmark.created_at', 'outlet_id', 'offer_id', 'outlet_offer_id' )
		->where(array('user_id'=> $_SESSION['user_id']))
		->join('outlet_offer', 'bookmark.outlet_offer_id', '=','outlet_offer.id' )
		->join('outlet', 'outlet.id', '=','outlet_offer.outlet_id' )
// 		->toSql();
		->orderBy('bookmark.created_at', 'desc')
		->get();
		
		if($result->isEmpty())
			$status=  self::SUCCESS_OK_NO_CONTENT;
		else
			$status = self::SUCCESS_OK;

		return $this->sendResponse ( $result, $status);
		
	}
	// list all user
	public function listAll(Request $request) {
		
		$result = Offer::getAllOffers($_GET);	

		return $this->sendResponse ( $result );
	}
	
	public function searchDB($searchText , $entity = 'restaurant', $options=array()){
		
		$searchText = urldecode($searchText);
		$result = Offer::getAllOffers(array('search'=>$searchText));
		return $this->sendResponse ( $result);
	}

	public function search($searchText , $entity = 'restaurant', $options=array()){
	
		
		$searchText =urldecode($searchText);
// 		echo $searchText;
		
		$searchText = trim($searchText);
		$searchText = trim($searchText, ',');
		$searchText = strtolower($searchText);
	
		@$searchArr = split(',', $searchText, 2);
		$search = array();
	
		switch ($entity){
			case 'restaurant':
	
				$indexType = '/restaurant';
	
				if(isset($searchArr[1])){
	
					$search['query']['bool']['must'][] = array( 'match'=> [ 'name'=>$searchArr[0] ] );
	
					$searchWords = explode(' ', $searchArr[1]);
	
					$search['query']['bool']['should'][] = array('wildcard'=> [ 'address'=> end($searchWords).'*' ] );
					$search['query']['bool']['should'][] = array('match'=> [ 'address'=> $searchArr[1] ] );
	
				}else{
	
					$searchWords = explode(' ', $searchArr[0]);
	
					if(trim(end($searchWords)) == trim($searchArr[0]))
						$search['query']['bool']['must'][] = array('wildcard'=> [ 'name'=> end($searchWords).'*' ] );
					else{
						$search['query']['bool']['should'][] = array('wildcard'=> [ 'name'=> end($searchWords).'*' ] );
						$search['query']['bool']['must'][] = array('match'=> [ 'name'=> $searchArr[0] ] );
					}
				}
	
// 				if(isset($options['isactivated']) and $options['isactivated'])
// 					$search['query']['bool']['must'][] = array('match'=> [ 'isactivated'=> true ] );
	
				break;
					
			default:
				$indexType = '';
				$searchWords = explode(' ', $searchText);
				$query = '{ "query": { "query_string": { "query": "'.
						end($searchWords).'* '.
						$searchText.
						'", "analyze_wildcard": true } } }';
	
				$search = json_decode($query, true);
					
		}
	
		$search["track_scores"] = true;
	
		$search['sort'][] = array( "_score" => "desc" );
	
		if(isset($options['location']) and !empty($options['location'])){
				
			$search['sort'][] = array("_geo_distance" => array(
					"location" => $options['location']['lat']. ', '.$options['location']['lon'] ,
					"order" => "asc",
					"unit" => "km"
			
			));
		}
	
		$searchurl = '/ft_privilege'.$indexType.'/_search';
			
// 	echo	
		$query = json_encode($search);
			
		$result = ES::request($query, $searchurl);
// 		$result = self::es($query, $searchurl);
		
		return $this->sendResponse ( $result->hits);
	}
	
	
	
	
	public function outletOffer($outlet_id) {
		
		$result = Offer::where('offer.is_disabled', '0' )->where('is_active', '1')
		->join('outlet_offer', 'offer.id', '=', 'outlet_offer.offer_id')
		->where('outlet_offer.is_disabled', '0' )
		->where('outlet_offer.outlet_id',  $outlet_id)->get()
		;
		
// 		->paginate(10);
		$res['offers'] = $result;
		$res['outlet'] = Outlet::find($outlet_id);
		
// 		$User = User::where ( 'is_disabled', '0' )->with('score')->where( 'city_id', $city )->orderBy('id', 'desc')->paginate ( $this->pageSize );		
		return $this->sendResponse ( $res );
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
	
	public function search1($text, $tags = null) {
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