<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class DBLog extends BaseModel
{
	protected $table = 'db_log';
// 	protected $primaryKey = 'id';
	protected $fillable = ['entity_id','entity_table', 'action','before_save', 'after_save', 'created_user_name','created_by'];
// 	protected $dates = ['start_date'];
	
}