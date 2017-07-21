<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class UserEvent extends BaseModel
{
	protected $table = 'user_event';
// 	protected $primaryKey = 'id';
	protected $fillable = ['user_id', 'event_name','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}