<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExpRefund extends BaseModel
{
	protected $table = 'exp_refund';
	
//  id is the REFID for Paytm
// 	protected $primaryKey = 'id';
	protected $fillable = ['id', 'exp_purchases_id', 'order_id', 'user_id', 'refund_status', 'txn_id', 'metadata', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}