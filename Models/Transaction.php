<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class Transaction extends Model
{
	protected $table = 'transaction';
    protected $fillable = ['transaction_id', 'payment_id', 'event_id', 'method', 'amount', 'metadata', 'buyer_email', 'buyer_name', 'created_by'];
    
    
//     public function events()
//     {
//     	return $this->hasMany('App\Models\Events');
//     }
}
?>