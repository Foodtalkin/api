<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use Carbon\Carbon;

class Subscription extends BaseModel
{
    protected $table = 'subscription';
// 	protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'email', 'city_id', 'subscription_type_id', 'expiry', 'metadata', 'disable_reason', 'is_disabled'];
// 	protected $dates = ['start_date'];
    protected $appends = ['is_expired_subscription'];

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

    /**
     * @return bool
     */
    public function getIsExpiredSubscriptionAttribute()
    {
        if ($this->getAttribute('subscription_type_id') == 1) {
            return (int) Carbon::now()->gte(Carbon::parse($this->getAttribute('expiry')));
        }

        return 0;
    }
}