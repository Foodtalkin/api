<?php

namespace App\Http\Controllers;

use App\Models\Privilege\PaytmOrder;
use App\Models\Privilege\Subscription;
use DB;
use App\Models\User;
use App\Models\Events;
use App\Models\Contest;
use App\Models\EventParticipant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller {

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


    // gets a user with id
    public function get(Request $request, $id, $with = false) {
        $user = User::find ( $id );

        $user ['app'] = $user->appInfo();

        $user ['score'] = $user->score;
// 		if ($user && $with == 'events') {
        $user ['events'] = $user->events;
// 		}
        return $this->sendResponse ( $user );
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
    public function delete(Request $request, $id) {
        $request->headers->add(['Accept' => 'application/json', 'Content-Type' => 'application/json']);
        $attributes = $request->getRawPost (true);

        $user = User::find ( $id );

        if (array_get($attributes, 'password') != 'fti@user123' || !$user) {
            return $this->sendResponse ( null );
        }

        if ($user) {
            Subscription::where('user_id', $user->id)
                ->delete();

            PaytmOrder::where('user_id', $user->id)
                ->delete();
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