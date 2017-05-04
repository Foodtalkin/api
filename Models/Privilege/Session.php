<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Session extends BaseModel
{
	protected $table = 'session';
	protected $primaryKey = 'session_id';
	protected $fillable = ['session_id','refresh_token', 'user_id'];
// 	protected $dates = ['start_date'];

	public function user()
	{
		return $this->belongsTo('App\Models\Privilege\User');
	}
	
	
}