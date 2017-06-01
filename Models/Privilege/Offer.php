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
	
	
	public function outletOffer()
	{
		return $this->hasMany('App\Models\Privilege\OutletOffer');
	}
	
	public function outlet()
	{
		return $this->belongsToMany('App\Models\Privilege\Outlet', 'restaurant_cuisine');
	}
	
	public static function getAllOffers($options=[]){
		
		
		
// 		$User = User::select(DB::raw('count(1) as cnt'))->where ( 'is_disabled', '0' )->with('score')->groupBy('id')
		$where = array(
				'offer.is_disabled'=> '0',
				'outlet.is_disabled'=> '0',
				'outlet_offer.is_disabled'=> '0',
				'restaurant.is_disabled'=> '0',
		);
		
		
		
			
// 		$result = self::where('1')->;

		$query = self::select(DB::raw(' count(DISTINCT offer.id) offer_count, GROUP_CONCAT(DISTINCT offer.id) as offer_ids, COUNT(DISTINCT outlet.id) outlet_count , GROUP_CONCAT(DISTINCT outlet.id) as outlet_ids , 
			restaurant.id as rid, restaurant.name, restaurant.cost, restaurant.one_liner, restaurant.card_image '))
		->where ( $where )
		->join('outlet_offer', 'offer.id', '=', 'outlet_offer.offer_id')
		->join('outlet', 'outlet.id', '=', 'outlet_offer.outlet_id')
		->join('restaurant', 'restaurant.id', '=', 'outlet.resturant_id')->groupBy('restaurant.id');

		
		if(isset($options['city_zone_id'])){
			$query->whereIn('outlet.city_zone_id', explode(',', $options['city_zone_id']));
		}
// 			$where['outlet.city_zone_id']=$options['city_zone_id'];
			
		
		if(isset($options['cuisine'] )) {
			
			$query->join('restaurant_cuisine', 'restaurant_cuisine.restaurant_id', '=','restaurant.id')
			->whereIn('restaurant_cuisine.cuisine_id', explode(',', $options['cuisine']));
		}
		
		if(isset($options['cost'] )) {
			

			$b=false;$m=false;$s=false;
			
			$cost = explode(',', $options['cost']);
			
			if(in_array('budget', $cost))
				$b = true;

			if(in_array('mid', $cost))
				$m = true;
			
			if(in_array('splurge', $cost))
				$s = true;
					
			if ($b and $m and $s){
			}
			elseif ($b and $m){
				$query->where('restaurant.cost', '<=', '1500');
			}elseif ($m and $s){
				$query->where('restaurant.cost', '>=', '500');
			}elseif($b and $s){
				$query->where(function ($query) {
					$query->where('restaurant.cost', '<=', '500')
					->orWhere('restaurant.cost', '>=', '1599');
				});
				
			}elseif ($b){
				$query->where('restaurant.cost', '<=', '500');
			}elseif ($m){
				$query->whereBetween('restaurant.cost', ['500', '1500']);
			}elseif ($s){
				$query->where('restaurant.cost', '>=', '1599');
			}
			
// 			if($options['cost']=='budget')
// 				$query->where('restaurant.cost', '<=', '500');
// 			elseif ($options['cost']=='mid')
// 				$query->whereBetween('restaurant.cost', ['500', '1500']);
// 			else
// 				$query->where('restaurant.cost', '>=', '1599');
			
		}
		
// 		echo $query->toSql();
		
		$result = $query->paginate(self::PAGE_SIZE);
		
// 	$sql=	'SELECT count(DISTINCT offer.id) offer_count, GROUP_CONCAT(DISTINCT offer.id) as offer_ids, COUNT(DISTINCT outlet.id) outlet_count , GROUP_CONCAT(DISTINCT outlet.id) as outlet_ids , 
// 			restaurant.id, restaurant.name, restaurant.cost, restaurant.description, restaurant.cover_image, restaurant.card_image 
// 			from offer INNER JOIN outlet_offer on outlet_offer.offer_id = offer.id 
// 			INNER JOIN outlet on outlet.id = outlet_offer.outlet_id 
// 			INNER JOIN restaurant WHERE restaurant.id = outlet.resturant_id GROUP BY restaurant.id ';
	
// 	$result = DB::connection('ft_privilege')->select(DB::raw($sql))->paginate(10);
	
	if(empty($result))
		return null;
	else
		return $result;
		
		
	}
	
	
	public static function getOfferWithOutlet($outlet_id, $offer_id){
		
		$result = self::select(
				DB::raw( isset($_SESSION['user_id']) ? '(select count(1) from bookmark b where outlet_offer.id = b.outlet_offer_id and b.user_id = '.$_SESSION['user_id'].' ) as is_bookmarked': '0 as is_bookmarked' ),
				'outlet_offer.id as outlet_offer_id',
				'offer.id as offer_id', 
				'outlet.id as outlet_id',
				'restaurant.id as restaurant_id',
				'outlet.name as outlet_name', 
				'outlet_offer.cover_image',
				'latitude', 'longitude',
				'outlet.phone', 
				'area', 'postcode', 
				'restaurant.cost',
				'outlet_offer.short_description as short_description',
				'address',
				'work_hours', 
				'outlet_offer.description as description',
				'outlet.suggested_dishes',
				'term_conditions_link', 
				'offer.title as offer_title',
				'outlet.metadata',
				'start_date', 'end_date',

				DB::raw(
						isset($_SESSION['user_id'])?
						'purchase_limit - IFNULL((SELECT SUM(offers_redeemed) as total_offer_redeemed FROM `offer_redeemed` WHERE offer_redeemed.offer_id = outlet_offer.offer_id and offer_redeemed.outlet_id = outlet_offer.outlet_id and offer_redeemed.user_id = '.$_SESSION['user_id'].'), 0) as purchase_limit' : 'purchase_limit' )
// 				'purchase_limit', 'limit_per_purchase'
// 				, 'type'
				)
		->where ( 'offer.is_disabled', '0' )
		->join('outlet_offer', 'offer.id', '=', 'outlet_offer.offer_id')
		->join('outlet', 'outlet.id', '=', 'outlet_offer.outlet_id')
		->join('restaurant', 'restaurant.id', '=', 'outlet.resturant_id')
		->where ( 'offer.id', $offer_id )
		->where ( 'outlet.id', $outlet_id )
		->first()
		;
// 		echo $result->toSql();
		
		if(empty($result))
			return null;
		else
			return $result;
	}
	
}