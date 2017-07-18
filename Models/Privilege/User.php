<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class User extends BaseModel
{
	protected $table = 'user';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'email','phone', 'gender', 'dob', 'saving', 'is_verified', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	public function session()
	{
		return $this->hasOne('App\Models\Privilege\Session');
	}
	
	public function subscription()
	{
		return $this->hasMany('App\Models\Privilege\Subscription');
	}
	
	public function offerRedeemed(){
		return $this->hasMany('App\Models\Privilege\OfferRedeemed');
	}
	
}