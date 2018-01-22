<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class PaytmOrder extends BaseModel
{
	protected $table = 'paytm_order';
// 	protected $primaryKey = 'id';
	protected $fillable = [
	    'id',
        'subscription_type_id',
        'user_id','channel',
        'txn_amount',
        'ori_amount',
        'coupon_id',
        'coupon_amount',
        'is_disabled',
        'created_by'
    ];
// 	protected $dates = ['start_date'];
	
}