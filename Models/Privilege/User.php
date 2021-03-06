<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use Carbon\Carbon;

class User extends BaseModel
{
    protected $table = 'user';
// 	protected $primaryKey = 'id';
    protected $fillable = ['name', 'email','phone', 'gender', 'preference', 'city_id', 'dob', 'saving', 'notes', 'is_verified', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

    public function session()
    {
        return $this->hasOne('App\Models\Privilege\Session');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\Privilege\City');
    }

    public function allSubscription()
    {
        return $this->hasMany('App\Models\Privilege\Subscription');
    }

    public function subscription()
    {
// 		return $this->hasMany('App\Models\Privilege\Subscription')->where('expiry', '>', date('Y-m-d').' 00:00:00')->orderBy('expiry', 'desc')->limit('1');
        return $this->hasMany('App\Models\Privilege\Subscription')->orderBy('expiry', 'desc')->limit('1');
    }

    public function offerRedeemed(){
        return $this->hasMany('App\Models\Privilege\OfferRedeemed');
    }

    /**
     * @param $subscription
     * @return bool
     */
    public function isSubscriptionExpired($subscription)
    {
        if ($subscription) {
            return $subscription->is_expired_subscription;
        }

        return false;
    }
}