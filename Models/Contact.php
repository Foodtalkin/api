<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Contact extends BaseModel
{
	protected $table = 'contact'; 
// 	protected $primaryKey = 'id';
	protected $fillable = ['name', 'email', 'phone', 'web', 'purpose', 'location', 'message', 'status', 'dateTime'];
     
}