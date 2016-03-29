<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class Template extends Model
{
	protected $table = 'template';
    protected $fillable = ['name', 'desc', 'preview_image', 'metadata', 'is_disabled'];
    
    
    public function events()
    {
    	return $this->hasMany('App\Models\Events');
    }
}
?>