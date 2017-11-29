<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExpPurchases extends BaseModel
{
	protected $table = 'exp_purchases';
// 	protected $primaryKey = 'id';
	protected $fillable = ['order_id', 'user_id', 'payment_status', 'refunded', 'txn_id', 'metadata', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	public function order()
	{
		return $this->belongsTo('App\Models\Privilege\ExpPurchasesOrder', 'order_id');
	}
	
}