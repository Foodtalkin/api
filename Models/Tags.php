<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Tags extends BaseModel
{
	protected $table = 'tags'; 
// 	protected $primaryKey = ['events_id', 'tag_name'];
	protected $fillable = ['events_id', 'tag_name'];
	
	
	public function events()
	{
		return $this->belongsTo('App\Models\Events');
	}     
}