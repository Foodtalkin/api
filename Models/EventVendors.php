<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class EventVendors extends BaseModel
{
	protected $table = 'event_vendors'; 
	protected $primaryKey = ['events_id', 'vendors_id'];
	protected $fillable = ['events_id', 'vendors_id'];
	
	
	public function events()
	{
		return $this->belongsTo('App\Models\Events');
	}
	
	public function vendors()
	{
		return $this->belongsTo('App\Models\Vendors');
	}
     
}