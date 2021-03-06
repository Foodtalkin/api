<?php
namespace App\Http\Controllers\Privilege;

use App\Models\Privilege\Coupon;
use Carbon\Carbon;

trait CouponTrait
{
    function getCoupon($attributes)
    {
        $coupon = null;
        if (array_get($attributes, 'coupon_code')) {
            $coupon = Coupon::where('code', array_get($attributes, 'coupon_code'))
                ->where('qty', '>', 0)
                ->where('is_active', true)
                ->where('is_disabled', false)
                ->where('expire_at', '>=', Carbon::now()->format('Y-m-d'))
                ->first();

            if (! $coupon) {
                return $this->sendResponse( 'ERROR! : Coupon code is invalid or expired!',  self::NOT_ACCEPTABLE, 'OOPS! Coupon code is invalid or expired.');
            }
        }

        return $coupon;
    }
}