<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\Privilege\Base\BaseModel;

class Offer extends BaseModel
{
	protected $table = 'offer';
// 	protected $primaryKey = 'id';
	protected $fillable = ['title', 'cover_image', 'card_image', 'action_button_text', 'card_action_button_text', 'description', 'short_description', 'term_conditions_link', 'thankyou_text', 'start_date', 'end_date', 'purchase_limit', 'limit_per_purchase', 'type', 'is_active', 'is_disabled', 'disable_reason', 'created_by'];
	
	protected $dates = ['start_date', 'end_date'];
	
	
	public function outlet()
	{
		return $this->belongsToMany('App\Models\Privilege\Outlet', 'outlet_offer');
	}
	
	public static function getAllOffers(){
		
		
		
// 		$User = User::select(DB::raw('count(1) as cnt'))->where ( 'is_disabled', '0' )->with('score')->groupBy('id')
		
// 		$result = self::where('1')->;

		$result = self::select(DB::raw(' count(DISTINCT offer.id) offer_count, GROUP_CONCAT(DISTINCT offer.id) as offer_ids, COUNT(DISTINCT outlet.id) outlet_count , GROUP_CONCAT(DISTINCT outlet.id) as outlet_ids , 
			restaurant.id as rid, restaurant.name, restaurant.cost, restaurant.description, restaurant.cover_image, restaurant.card_image '))
		->where ( 'offer.is_disabled', '0' )
		->join('outlet_offer', 'offer.id', '=', 'outlet_offer.offer_id')
		->join('outlet', 'outlet.id', '=', 'outlet_offer.outlet_id')
		->join('restaurant', 'restaurant.id', '=', 'outlet.resturant_id')->groupBy('restaurant.id')
		->paginate(10);
		
	$sql=	'SELECT count(DISTINCT offer.id) offer_count, GROUP_CONCAT(DISTINCT offer.id) as offer_ids, COUNT(DISTINCT outlet.id) outlet_count , GROUP_CONCAT(DISTINCT outlet.id) as outlet_ids , 
			restaurant.id, restaurant.name, restaurant.cost, restaurant.description, restaurant.cover_image, restaurant.card_image 
			from offer INNER JOIN outlet_offer on outlet_offer.offer_id = offer.id 
			INNER JOIN outlet on outlet.id = outlet_offer.outlet_id 
			INNER JOIN restaurant WHERE restaurant.id = outlet.resturant_id GROUP BY restaurant.id ';
	
// 	$result = DB::connection('ft_privilege')->select(DB::raw($sql))->paginate(10);
	
	if(empty($result))
		return null;
	else
		return $result;
		
		
	}
	
}