<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Cuisine extends BaseModel
{
	protected $table = 'cuisine';
// 	protected $primaryKey = 'id';
	protected $fillable = ['title','alt','description','parent', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	public function restaurant(){
		return $this->belongsToMany('App\Models\Privilege\Restaurant','restaurant_cuisine');
	}
}