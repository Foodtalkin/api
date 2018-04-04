<?php

namespace App\Http\Controllers\Privilege;


use App\Models\Privilege\OfferRedeemed;
use App\Models\Privilege\Outlet;
use App\Models\Privilege\Image;
use App\Models\User;
use App\Models\Events;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $attributes['pin'] = rand(1000, 9999);
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

    // list all iamges
    public function getAllImages(Request $request, $id) {
        $result = Image::where(['entity'=>'outlet', 'entity_id'=>$id])->get();
        return $this->sendResponse ( $result );
    }


    public function addImages(Request $request, $id){

        $attributes = $request->getRawPost(true);
        foreach ($attributes['images'] as $image){

            $data = array();
            $data['entity_id'] = $id;
            $data['entity'] = 'outlet';
            $data['url'] =  $image['url'];
            $data['type'] = isset($image['type']) ? $image['type'] : 'menu';

            if(isset($data['title']))
                $data['title'] = $image['title'];

            $result = Image::create($data);
        }
        return $this->sendResponse ( true );
    }

    public function deleteImage($imageId) {
        $result = Image::find ( $imageId);

        if ($result) {
            $result->delete();
            return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Image deleted' );
        } else {
            return $this->sendResponse ( null );
        }
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

    /**
     * @param int $outletId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRedeemedRecord($outletId)
    {
        $result = OfferRedeemed::selectRaw('user.name as user, offer_redeemed.id, offer.title, offer_redeemed.offers_redeemed, offer_redeemed.created_at')
            ->join('offer', 'offer.id', '=', 'offer_redeemed.offer_id')
            ->leftJoin('user', 'user.id', '=', 'offer_redeemed.user_id')
            ->where('offer_redeemed.outlet_id', '=', $outletId)
            ->oldest('offer_redeemed.created_at')
            ->limit(500)
            ->get();

        return $this->sendResponse ( $result );
    }

}
?>