<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class City extends BaseModel
{
	protected $table = 'city';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}