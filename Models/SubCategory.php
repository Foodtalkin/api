<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class SubCategory extends Model
{
	protected $table = 'sub_category';
    protected $fillable = ['name', 'desc'];
    
    
    public function events()
    {
    	return $this->hasMany('App\Models\Events');
    }
}
?>