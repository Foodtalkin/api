<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class RestaurantCuisine extends BaseModel
{
	protected $table = 'restaurant_cuisine';
// 	protected $primaryKey = 'id';
	protected $fillable = ['restaurant_id', 'cuisine_id', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	public function Cuisine()
	{
		return $this->belongsTo('App\Models\Privilege\Cuisine');
	}
	
	public function Restaurant()
	{
		return $this->belongsTo('App\Models\Privilege\Restaurant');
	}
	
}