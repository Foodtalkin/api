<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class OutletOffer extends BaseModel
{
	protected $table = 'outlet_offer';
// 	protected $primaryKey = 'id';
	protected $fillable = ['title', 'cover_image', 'card_image', 'action_button_text', 'card_action_button_text', 'description', 'short_description', 'term_conditions_link', 'thankyou_text', 'start_date', 'end_date', 'purchase_limit', 'limit_per_purchase', 'type', 'is_active', 'is_disabled', 'disable_reason', 'created_by'];
	
	protected $dates = ['start_date', 'end_date'];
	
	
	public function offer()
	{
		return $this->belongsTo('App\Models\Privilege\Offer');
	}
	
	public function outlet()
	{
		return $this->belongsTo('App\Models\Privilege\Outlet');
	}

}

