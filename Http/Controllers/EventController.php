<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Events;
use App\Models\Contest;
use App\Models\Tags;
use App\Models\EventVendors;
use App\Models\EventPartners;
use App\Models\Partners;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendors;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EventController extends Controller {
	
	public function listAll($type, $status = 'all', $options = array()) {

		
		if(strtolower($status) == 'disabled')
			$rawSql = '1 ';
		else 
			$rawSql = '0 ';
		
		if(strtolower($status) == 'pending'){
			$rawSql .= ' AND active = 0 ';
		}
		if(strtolower($status) == 'active'){
			$rawSql .= ' AND active = 1 ';
		}
		
		
		$where = array();
		
		//     	DB::connection()->enableQueryLog();		
		switch ($type) {
			case 'event' :
				
				$where['type'] = $type;
				
				if(strtolower($status) == 'upcomming')
					$rawSql .= '  AND active = 1  AND  start_date >= CURDATE() ';
				if(strtolower($status) == 'past')
					$rawSql .= '  AND active = 1 AND  start_date <= CURDATE() ';
				
				$events = Events::where  ('is_disabled','=', DB::raw( $rawSql) )-> where( $where ) ->orderBy('start_date', 'desc')->paginate ( $this->pageSize );
				
				break;
			case 'contest' :
				$where['type'] = $type;
				
				if(strtolower($status) == 'ongoing')
					$rawSql .= ' AND active = 1 AND end_date > NOW() AND start_date <= CURDATE() ';
				
				$events = Contest::where ('is_disabled','=', DB::raw( $rawSql) )-> where( $where ) ->orderBy('start_date', 'desc')->paginate ( $this->pageSize );
				break;
		}
// 		        $que = DB::connection()->getQueryLog();
// 		        var_dump($que);
		
		if(isset($options['scope']) && $options['scope']='local'){
			return $events;
		}
		
		return $this->sendResponse ( $events );
	}
	
	
	public function getMobileAppFeeds(){
		
		$events = $this->listAll('event', 'upcomming', array('scope'=>'local'));
		$contest = $this->listAll('contest', 'ongoing', array('scope'=>'local'));
		
		$result=array();
		
		foreach ($events as $val){
		
			
			$result['data'][] = array(
				'url' => 'https://store.foodtalk.in/?type=event&target='.$val['id'].'&',
				'name' => $val['name'],
				'type' => $val['type'],
				'event_date' => $val['start_date']->format('Y-m-d'),					
			);
		}
		
		foreach ($contest as $val){
		
			$result['data'][] = array(
				'url' => 'https://store.foodtalk.in/?type=contest&target='.$val['id'].'&',
				'name' => $val['name'],
				'type' => $val['type'],
				'event_date' => $val['start_date']->format('Y-m-d'),					
			);
		}
		return $this->sendResponse ( $result );
	}
	
	
	public function get(Request $request, $type, $id, $with = false) {
				
		$events = array();
		// $users = DB::select('select id, name from events');
		// print_r($users);
		// 7500000103905114 53383
		
		// var_dump($request->headers);
		
		$is_admin = false;
		
// 		echo $_SESSION ['admin_id'];
		
		if (isset ($_SESSION ['admin_id'] )){
			$is_admin = true;
		}

		$rawSql = "$id";
// 		    	DB::connection()->enableQueryLog();
		
			switch ($type) {
				case 'event' :
					
					if(!$is_admin)
						$rawSql .= ' AND is_disabled = 0 AND active = 1 AND  start_date >= CURDATE()  ';
					
					$e = Events::where ('id' ,'=', DB::raw( $rawSql))->where ( array ('type' => $type) )->get ();
					
					break;
				case 'contest' :
					if(!$is_admin)
						$rawSql .= ' AND is_disabled = 0 AND active = 1 AND end_date >= CURDATE() AND start_date <= CURDATE() ';
					
					$e = Events::where ('id' ,'=', DB::raw( $rawSql))->where ( array ('type' => $type) )->get ();
// 					$e = Contest::where ( array ('id' => $id, 'type' => $type) )->get ();
					break;
			}		

		if(isset($e[0]))
			$events = $e[0];

		
		if (( bool ) $events && $type == 'event') {
			$events ['sub_category'] = $events->subCategory;
		}
		
		if((bool) $events)
			$events ['template'] = $events->template;
		
		if((bool) $events)
			$events ['partners'] = $events->partners;
		
		if((bool) $events && $is_admin)
			$events ['vendors'] = $events->vendors;
		
		
		if((bool) $events && $is_admin){			
			$events ['tags'] =
			$events->tags;
		}
		
		
		
		if (( bool ) $events ) {
			$events ['total_participants'] = $events->participants->count();
			if($with != 'participants'){
				unset($events ['participants']);
			}
		}
			
		
// 				        $que = DB::connection()->getQueryLog();
// 				        var_dump($que);
		
		return $this->sendResponse ( $events );
	}
	
	public function addVendors(Request $request, $type, $id) {
		
		$requestArr = $this->getResponseArr ( $request );
		
		$result = array();
		$vendor = Vendors::find ( $requestArr ['vendors_id'] );
		$events = Events::where ( array ('id' => $id, 'type' => $type) )->first ();		
		
		
		if ($vendor && $events){
			
			$eventVendors = EventVendors::where(array('events_id'=>$id,'vendors_id'=>$requestArr ['vendors_id']))->first ();;
			
			if($eventVendors){
				return $this->sendResponse ( null, self::NOT_ACCEPTABLE, 'Same Vendor Already present');
			}
			
			$result = Events::find ( $id )->vendors ()->save ( $vendor );
// 			$result = Vendors::find ( $id )->events ()->save ( $events, $participant );
		}
		return $this->sendResponse ( $result );
	}
	
	
	public function deleteVendors(Request $request, $type, $id, $vendors_id) {
	
		$requestArr = $this->getResponseArr ( $request );
	
			$eventVendors = EventVendors::where(array('events_id'=>$id,'vendors_id'=>$vendors_id))->first ();;
			
			if ($eventVendors) {
				EventVendors::where(array('events_id'=>$id,'vendors_id'=>$vendors_id))->delete();
				return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Vendor Removed' );
			} else {
				return $this->sendResponse ( null );
			}
	}
	
	
// 	add paretners
	public function addPartners(Request $request, $type, $id) {
	
		$requestArr = $this->getResponseArr ( $request );
	
		$result = array();
		$partners = Partners::find ( $requestArr ['partners_id'] );
		$events = Events::where ( array ('id' => $id, 'type' => $type) )->first ();
	
	
		if ($partners && $events){
				
			$EventPartners = EventPartners::where(array('events_id'=>$id,'partners_id'=>$requestArr ['partners_id']))->first ();;
				
			if($EventPartners){
				return $this->sendResponse ( null, self::NOT_ACCEPTABLE, 'Same Partners Already present');
			}
				
			$result = Events::find ( $id )->partners ()->save ( $partners);
			// 			$result = Vendors::find ( $id )->events ()->save ( $events, $participant );
		}
		return $this->sendResponse ( $result );
	}
	
// 	Remove a paretners
	public function deletePartners(Request $request, $type, $id, $partners_id) {
	
		$requestArr = $this->getResponseArr ( $request );
	
		$EventPartners = EventPartners::where(array('events_id'=>$id,'partners_id'=>$partners_id))->first ();;
			
		if ($EventPartners) {
			EventPartners::where(array('events_id'=>$id,'partners_id'=>$partners_id))->delete();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Partner Removed' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	
	public function addTags(Request $request, $id) {
		
			$requestArr = $this->getResponseArr ( $request );				
			$event = $this->createTags($requestArr['tags'], $id);
			
			return $this->sendResponse ( $event );
			
	}
	
	private function createTags(array $tags, $eventId) {
		
		$tags = array_unique(array_map('strtolower',$tags));
		
// 		delete old one
		$oldTags= Tags::where(array('events_id'=>$eventId))->delete();
		
		$event = Events::find ( $eventId );

		foreach ($tags as $tag){
			$tag = new Tags(['tag_name' => $tag]);
			$event->tags()->save($tag);
		}		
		return  $event;
	}	
	
	
	public function create(Request $request, $type) {
		
		$requestArr = $this->getResponseArr ( $request );
		switch ($type) {
			case 'event' :
				$Events = Events::create ( $requestArr );
				break;
			case 'contest' :
				$Events = Contest::create ( $requestArr );
				break;
		}
		if(isset($requestArr['tags']) && !empty($requestArr['tags']))
		$event = $this->createTags($requestArr['tags'], $Events->id);
		
		return $this->sendResponse ( $Events );
	}
	
	public function delete($type, $id) {
		switch ($type) {
			case 'event' :
				$Events = Events::find ( $id );
				break;
			case 'contest' :
				$Events = Contest::find ( $id );
				break;
		}
		

		
		
		if ($Events) {
			$Events->delete ();
			$oldTags = Tags::where(array('events_id'=>$id))->delete();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	public function update(Request $request, $id, $type ) {
		$requestArr = $this->getResponseArr ( $request );
		
// 		$Events = Events::find ( $id );
		
		switch ($type) {
			case 'event' :
				$Events = Events::find ( $id );
				break;
			case 'contest' :
				$Events = Contest::find ( $id );
				break;
		}		
		
		$Events->update($requestArr);
    	
		if(isset($requestArr['tags']) && !empty($requestArr['tags']))
			$event = $this->createTags($requestArr['tags'], $Events->id);
		
		return $this->sendResponse ( $Events );
    }
  
}
?>