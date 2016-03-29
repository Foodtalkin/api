<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Vendors extends BaseModel
{
	protected $table = 'vendors'; 
// 	protected $primaryKey = 'eid';
	protected $fillable = [
			'name', 'orgnaization', 'loaction',  'address', 'email', 'phone' , 'publication'
			, 'designation', 'website', 'blog' ,'url' ,'vendors_category_id', 'capicity', 'type', 'is_disabled', 'created_by'
			];
// 	protected $dates = ['start_date', 'end_date'];
// 	protected $hidden = [ 'password' ];
	
	
	public function events()
	{
		return $this->belongsToMany('App\Models\Events', 'event_vendors')
		->withPivot('created_at', 'updated_at')
		;
	}
	
	public function category()
	{
		return $this->belongsTo('App\Models\VendorsCategory', 'vendors_category_id');
	}
	
// 	public static function create(array $attributes = [])
// 	{
// 		$attributes['type'] = 'contest';
// 		$attributes['active'] = 1;
// 		return parent::create($attributes);
// 	} 
	
}
?>