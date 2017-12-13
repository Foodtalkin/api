<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class PaytmOrderStatus extends BaseModel
{
	protected $table = 'paytm_order_status';
// 	protected $primaryKey = 'id';
	protected $fillable = ['paytm_order_id', 'subscription_id', 'payment_status', 'txn_id', 'metadata', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}