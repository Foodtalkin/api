<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Restaurant extends BaseModel
{
	
	protected $table = 'restaurant';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'cost','description', 'one_liner', 'cover_image', 'card_image', 'primary_cuisine', 'disable_reason', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	
	public function primaryCuisine(){
			return $this->belongsTo('App\Models\Privilege\Cuisine', 'primary_cuisine');
	}
	
	public function cuisine(){
		return $this->belongsToMany('App\Models\Privilege\Cuisine', 'restaurant_cuisine')->select('cuisine.id', 'cuisine.title')->orderBy('cuisine.title', 'asc');
	}

	public function outlet(){
		return $this->hasMany('App\Models\Privilege\Outlet', 'resturant_id')->orderBy('outlet.name', 'asc');
	}
	
}