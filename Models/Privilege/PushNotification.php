<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class PushNotification extends BaseModel
{
	protected $table = 'push_notification';
	// 	protected $primaryKey = 'id';
	protected $fillable = ['push_time', 'push', 'metadata', 'status', 'is_disabled', 'created_by'];
	// 	protected $dates = ['start_date'];
	
}