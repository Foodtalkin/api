<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class ActivityScore extends BaseModel
{
	protected $table = 'activity_score'; 
	protected $primaryKey = 'facebookId';
	protected $fillable = ['avilablePoints','totalPoints', 'score'];
     
}