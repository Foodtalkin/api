<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Events extends BaseModel
{
	protected $table = 'events'; 
// 	protected $primaryKey = 'eid';
	protected $fillable = ['name', 'start_date', 'location',
							'venue', 'thumb_url', 'cover_url', 'payment_url', 'description', 'details', 'sold_out', 'album_url',
							'cost','capacity', 'timings', 'type', 'sub_category_id', 'colobrators', 'template_id' ,'active', 'is_disabled', 'metadata', 'created_by'];
	protected $dates = ['start_date'];
// 	protected $hidden = [ 'password' ];
	
	
	public function participants($order=array())
	{
		
		
		$result = $this->belongsToMany('App\Models\User', 'event_participant')->withPivot('email', 'response','contact', 'payment', 'quantity', 'source', 'created_at')->score;
		
		
		if(isset($order['created_at'])){
			$result->orderBy('pivot_created_at', 'asc');
		}else 		
			$result->orderBy('pivot_created_at', 'desc');
		
		return $result;
	}
		
	
	public function subCategory()
	{
		return $this->belongsTo('App\Models\SubCategory');
	}
	
	public function template()
	{
		return $this->belongsTo('App\Models\Template');
	}
	
	
	public function vendors()
	{
		return $this->belongsToMany('App\Models\Vendors', 'event_vendors');
	}
	
	public function partners()
	{
		return $this->belongsToMany('App\Models\Partners', 'event_partners');
	}
	
	
	public function tags()
	{
		return $this->hasMany('App\Models\Tags');
	}
	
	
	public static function create(array $attributes = [])
	{
		$attributes['type'] = 'event';
		$attributes['active'] = 0;
		$attributes['is_disabled'] = 0;
		return parent::create($attributes);
	} 
	
}
?>