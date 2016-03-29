<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class EventPartners extends BaseModel
{
	protected $table = 'event_partners'; 
// 	protected $primaryKey = 'id';
	protected $fillable = ['events_id', 'partners_id', 'created_by', 'metadata'];
	
	
	public function event()
	{
		return $this->belongsTo('App\Models\Events');
	}
	
	public function partners()
	{
		return $this->belongsTo('App\Models\Partners');
	}
     
}