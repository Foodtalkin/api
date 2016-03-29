<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Contest extends BaseModel
{
	protected $table = 'events'; 
// 	protected $primaryKey = 'eid';
// 	protected $fillable = ['name', 'start_date', 'end_date', 'type', 'colobrators', 'active', 'metadata'];
	
	protected $fillable = ['name', 'start_date', 'end_date', 'location',
			'venue', 'thumb_url', 'cover_url', 'payment_url', 'description', 'details', 'sold_out', 'album_url',
			'cost','capacity', 'timings', 'type', 'sub_category_id', 'colobrators', 'template_id' ,'active', 'is_disabled', 'metadata', 'created_by'];
	
	protected $dates = ['start_date', 'end_date'];
// 	protected $hidden = [ 'password' ];
	
	
	public function participants()
	{
		return $this->belongsToMany('App\Models\User', 'event_participant', 'events_id')->withPivot('email', 'contact', 'payment', 'quantity', 'response', 'created_at');
	}
	
	public static function create(array $attributes = [])
	{
		$attributes['type'] = 'contest';
		$attributes['active'] = 1;
		return parent::create($attributes);
	} 
	
}
?>