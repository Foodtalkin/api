<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class City extends BaseModel
{
	protected $table = 'city'; 
// 	protected $primaryKey = 'id';
	protected $fillable = ['name','is_disabled', 'created_by'];
     
}