<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class EventParticipant extends BaseModel
{
	protected $table = 'event_participant'; 
// 	protected $primaryKey = 'id';
	protected $fillable = ['transaction_id', 'payment_id', 'events_id', 'user_id', 'email', 'subscribe', 'contact', 'payment_method','quantity', 'payment', 'response', 'source', 'metadata' ];
	
	
	public function event()
	{
		return $this->belongsTo('App\Models\Events');
	}
	
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
     
}
