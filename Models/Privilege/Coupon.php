<?php namespace App\Models\Privilege;
  
use App\Models\Privilege\Base\BaseModel;

class Coupon extends BaseModel
{
    /**
     * @var string
     */
	protected $table = 'coupons';

    /**
     * @var array
     */
	protected $fillable = ['code', 'discount', 'duration', 'qty', 'created_by', 'expire_at', 'is_active', 'is_disabled'];

    /**
     * @param SubscriptionType $type
     * @return object
     */
	public function estimateSubscription(SubscriptionType $type)
    {
        $couponAmt = floor(($type->price * $this->discount) / 100);

        return (object) array_merge($this->toArray(), [
            'coupon_amount' => $couponAmt,
            'txt_amount' => $type->price - $couponAmt
        ]);
    }
}