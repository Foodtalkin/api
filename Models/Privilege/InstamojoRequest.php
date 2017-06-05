<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class InstamojoRequest extends BaseModel
{
	protected $table = 'instamojo_request';
// 	protected $primaryKey = 'id';
	protected $fillable = ['user_id','payment_id', 'subscription_type_id', 'amount', 'status', 'payment_url', 'metadata', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}