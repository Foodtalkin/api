<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class User extends BaseModel
{
	protected $table = 'user';
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'email','phone', 'gender', 'dob', 'is_verified', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
}