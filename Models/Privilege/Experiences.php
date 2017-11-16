<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use DB;
use \stdClass;
class Experiences extends BaseModel
{
	protected $table = 'experiences';
// 	protected $primaryKey = 'id';
	protected $fillable = ['title', 'cover_image', 'card_image', 'address', 'city_id', 'latitude', 'longitude', 'start_time', 'end_time', 'cost', 'nonveg_preference', 'taxes', 'convenience_fee', 'total_seats', 'action_text', 'tag', 'is_active','is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];
	
	
	public function data()
	{
		return $this->hasMany('App\Models\Privilege\ExpData', 'exp_id')->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
// 		return $this->belongsTo('App\Models\Privilege\ExpData');
	}
	
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}
	
	public function estimateCost( $tickets = 1 ){
		
		$result = new stdClass();
		$result->cost_for_one = $this->cost;
		$result->tickets = $tickets;
		$result->cost = $this->cost * $tickets;
		$result->convenience_fee = $this->convenience_fee * $tickets;
		$result->taxes = $result->convenience_fee * $this->taxes / 100;
		$result->amount = $result->cost + $result->convenience_fee + $result->taxes;
		return $result;
	}

	public function seats($userId = null){
		
		ExperiencesSeats::where('created_at', '<', DB::raw('DATE_SUB(NOW() , INTERVAL 10 MINUTE)'))->delete();
		
		if($userId)
			return $this->hasMany('App\Models\Privilege\ExperiencesSeats', 'exp_id')->where('user_id', '!=',$userId);
		else 	
			return $this->hasMany('App\Models\Privilege\ExperiencesSeats', 'exp_id');
	}
	
}