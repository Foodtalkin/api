<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class OfferRedeemed extends BaseModel
{
	protected $table = 'offer_redeemed';
// 	protected $primaryKey = 'id';
	protected $fillable = ['offer_id','outlet_id','user_id','redeemed_by', 'offers_redeemed', 'saving', 'metadata',  'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];

	
	public function offer()
	{
		return $this->belongsTo('App\Models\Privilege\Offer');
	}
	
	public function outlet()
	{
		return $this->belongsTo('App\Models\Privilege\Outlet');
	}
	
	public function user()
	{
		return $this->belongsTo('App\Models\Privilege\User');
	}
	
}