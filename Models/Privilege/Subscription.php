<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Subscription extends BaseModel
{
	protected $table = 'subscription';
// 	protected $primaryKey = 'id';
	protected $fillable = ['user_id', 'email', 'city_id', 'subscription_type_id', 'expiry', 'metadata', 'disable_reason', 'is_disabled'];
// 	protected $dates = ['start_date'];

	public function user()
	{
		return $this->belongsTo('App\Models\Privilege\User');
	}
	
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}

	public function subscriptionType()
	{
		return $this->belongsTo('App\Models\Privilege\SubscriptionType');
	}
	
	
	
}