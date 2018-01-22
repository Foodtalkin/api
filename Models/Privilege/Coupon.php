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
	protected $fillable = ['code', 'amount', 'qty', 'created_by', 'expire_at', 'is_active', 'is_disabled'];
}