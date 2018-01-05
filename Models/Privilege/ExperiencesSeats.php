<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExperiencesSeats extends BaseModel
{
	protected $table = 'experiences_seats';
	protected $primaryKey = 'user_id';
	protected $fillable = ['exp_id', 'user_id', 'order_id', 'blocked_seats', 'created_by'];
// 	protected $dates = ['start_date'];
	
	
// 	public static function create(array $attributes = [])
// 	{
	
// 		if(strtoupper($attributes['type'])!= 'TEXT' and strtoupper($attributes['type'])!= 'URL' and strtoupper($attributes['type'])!= 'VIDEO')
// 			if(isset($attributes['content']))
// 				$attributes['content'] = json_encode($attributes['content']);
			
// 			return parent::create($attributes);
// 	}
	
	public function experiences()
	{
		return $this->belongsTo('App\Models\Privilege\Experiences');
	}
	
}