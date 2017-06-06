<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class InstamojoLog extends BaseModel
{
	protected $table = 'instamojo_log';
	protected $fillable = ['payment_id','instamojo_paymant_id', 'buyer_name', 'amount', 'status', 'phone', 'metadata'];
}