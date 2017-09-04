<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Outlet extends BaseModel
{
	protected $table = 'outlet';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'phone', 'email', 'suggested_dishes', 'address', 'city_id', 'city_zone_id', 'area', 'postcode', 'description', 'resturant_id', 'work_hours', 'pin', 'latitude', 'longitude', 'ft_resturantId', 'disable_reason', 'is_disabled', 'created_by', 'metadata'];
// 	protected $dates = ['start_date'];

	public function offer()
	{
		return $this->belongsToMany('App\Models\Privilege\Offer', 'outlet_offer')->withPivot('id', 'is_disabled');
	}
	
	public function resturant()
	{
		return $this->belongsTo('App\Models\Privilege\Restaurant');
	}
	
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}
	
// 	public function resturant()
// 	{
// 		return $this->belongsTo('App\Models\Privilege\Restaurant');
// 	}
	
	
	
}