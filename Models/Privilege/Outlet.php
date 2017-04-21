<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Outlet extends BaseModel
{
	protected $table = 'outlet';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'address', 'city_id', 'city_zone_id', 'area', 'postcode', 'description', 'resturant_id', 'work_hours', 'pin', 'latitude', 'longitude', 'ft_resturantId', 'disable_reason', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	
	public function offer()
	{
		return $this->belongsToMany('App\Models\Privilege\Offer', 'outlet_offer');
	}
	
	public function resturant()
	{
		return $this->belongsTo('App\Models\Privilege\Restaurant');
	}
	
	
}