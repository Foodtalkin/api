<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class InstamojoLog extends BaseModel
{
	protected $table = 'instamojo_log';
	protected $fillable = ['payment_id', 'amount', 'status', 'phone', 'metadata'];
}