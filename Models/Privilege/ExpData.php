<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class ExpData extends BaseModel
{
	protected $table = 'exp_data';
// 	protected $primaryKey = 'id';
	protected $fillable = ['exp_id', 'metedata', 'type', 'title', 'content', 'sort_order', 'created_by'];
// 	protected $dates = ['start_date'];
	
	
	public static function create(array $attributes = [])
	{
	
		if(strtoupper($attributes['type'])!= 'TEXT' and strtoupper($attributes['type'])!= 'URL' and strtoupper($attributes['type'])!= 'VIDEO')
			if(isset($attributes['content']))
				$attributes['content'] = json_encode($attributes['content']);
			
			return parent::create($attributes);
	}
	
	public function experiences()
	{
		return $this->belongsTo('App\Models\Privilege\Experiences');
	}
	
}