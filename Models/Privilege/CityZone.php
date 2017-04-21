<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class CityZone extends BaseModel
{
	protected $table = 'city_zone';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'description', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}
	
}