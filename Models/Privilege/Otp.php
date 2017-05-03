<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Otp extends BaseModel
{
	protected $table = 'otp';
	protected $primaryKey = 'phone';
	protected $fillable = ['otp','phone', 'created_by'];
// 	protected $dates = ['start_date'];
	
}