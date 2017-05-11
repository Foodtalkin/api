<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Image extends BaseModel
{
	protected $table = 'image';
// 	protected $primaryKey = 'id';
	protected $fillable = ['url','entity', 'entity_id', 'type', 'title', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}