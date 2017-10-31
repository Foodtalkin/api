<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExpPurchasesOrder extends BaseModel
{
	protected $table = 'exp_purchases_order';
// 	protected $primaryKey = 'id';
	protected $fillable = ['id', 'exp_id', 'user_id', 'total_tickets', 'non_veg', 'channel', 'txn_amount', 'taxes', 'convenience_fee' ,'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}