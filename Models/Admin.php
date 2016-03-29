<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Admin extends BaseModel
{
	protected $table = 'admin'; 
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'email', 'contact', 'password', 'address', 'role', 'metadata', 'active'];
     
}