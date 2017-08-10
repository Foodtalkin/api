<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Paytmlog extends BaseModel
{
	protected $table = 'paytmlog';
// 	protected $primaryKey = 'id';
	protected $fillable = ['metadata','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}