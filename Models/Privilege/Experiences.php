<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Experiences extends BaseModel
{
	protected $table = 'experiences';
// 	protected $primaryKey = 'id';
	protected $fillable = ['title', 'cover_image', 'card_image', 'address', 'city_id', 'start_time', 'end_time', 'cost', 'total_seats', 'action_text', 'tag', 'is_active','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	
	public function data()
	{
		return $this->hasMany('App\Models\Privilege\ExpData', 'exp_id')->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
// 		return $this->belongsTo('App\Models\Privilege\ExpData');
	}
	
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}
}