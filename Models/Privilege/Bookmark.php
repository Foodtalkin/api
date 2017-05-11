<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class Bookmark extends BaseModel
{
	protected $table = 'bookmark';
// 	protected $primaryKey = 'id';
	protected $fillable = ['user_id','outlet_offer_id','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	public function user()
	{
		return $this->belongsTo('App\Models\Privilege\User');
	}
	
	public function outletOffer()
	{
		return $this->belongsTo('App\Models\Privilege\OutletOffer');
	}
	
}