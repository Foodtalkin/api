<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;

class Partners extends BaseModel
{
	protected $table = 'partners'; 
// 	protected $primaryKey = 'uid';
	protected $fillable = ['name', 'desc', 'logo',  'metadata', 'is_disabled', 'created_by'];
// 	protected $hidden = [ 'password' ];
	
	
// 	public function particiption()
// 	{
// 		return $this->hasMany('App\Models\EventParticipant');
// 	}
	
	public function events()
	{
		
// 		$this->belongsToMany($related)
		return $this->belongsToMany('App\Models\Events', 'event_partners');
// 		->wherePivot('email', '=', 'email@email.com')
// 		->withPivot('email', 'contact', 'payment', 'quantity', 'response', 'created_at');
	}
	
}
?>