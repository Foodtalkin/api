<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExpPurchases extends BaseModel
{
	protected $table = 'exp_purchases';
// 	protected $primaryKey = 'id';
	protected $fillable = ['order_id', 'exp_id', 'rating', 'review', 'user_id', 'payment_status', 'refunded', 'txn_id', 'metadata', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	public function user()
	{
		return $this->belongsTo('App\Models\Privilege\User');
	}
	
	public function order()
	{
		return $this->belongsTo('App\Models\Privilege\ExpPurchasesOrder', 'order_id');
	}

	public function experiences()
	{
		return $this->belongsTo('App\Models\Privilege\Experiences', 'exp_id');
	}
	
}