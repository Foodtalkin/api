<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class SubscriptionType extends BaseModel
{
	protected $table = 'subscription_type';
// 	protected $primaryKey = 'id';
	protected $fillable = [ 'city_id', 'price', 'expiry_in_days', 'metadata', 'disable_reason', 'is_disabled'];
// 	protected $dates = ['start_date'];

	
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}
	
	
}