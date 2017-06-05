<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class InstamojoPayment extends BaseModel
{
	protected $table = 'instamojo_payment';
	protected $fillable = ['payment_id', 'amount', 'status', 'phone', 'metadata', 'is_disabled', 'created_by'];
	
}