<?php
namespace App\Http\Controllers;
  
use DB;
use App\Models\City;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;

class CityController extends Controller{
  
  
	public function listAll() {
				
		$city = City::orderBy('created_at', 'desc')->paginate ($this->pageSize);
		return $this->sendResponse ( $city );
	}
	
	// gets a user with id
	public function get(Request $request, $id,$type, $with = false) {
		
		$contact = Contact::find ( $id );		
		return $this->sendResponse ( $contact );
	}
  
    public function create(Request $request){

    	$attributes = $this->getResponseArr ( $request );
    	$contact = City::create ( $attributes );
    	return $this->sendResponse ( $contact );
    	
    }
  
	public function delete($id) {
		$contact = City::find ( $id );
		
		if ($contact) {
			$contact->delete ();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'entity deleted' );
		} else {
			return $this->sendResponse ( null );
		}
	}
  
    public function update(Request $request,$id){
    	$contact = City::find ( $id );
    	
    	if ($contact) {
    		$contact->update ( $this->getResponseArr ( $request ) );
    		return $this->sendResponse ( $contact );
    			
    	} else {
    		return $this->sendResponse ( null );
    	}
    }
  
}
?>