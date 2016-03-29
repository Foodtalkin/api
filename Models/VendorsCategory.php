<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class VendorsCategory extends Model
{
	protected $table = 'vendors_category';
    protected $fillable = ['name', 'desc', 'type', 'created_by'];
    
    
    public function vendors()
    {
    	return $this->hasMany('App\Models\Vendors');
    }
}
?>