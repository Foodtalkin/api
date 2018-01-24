<?php namespace App\Models\Privilege;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use DB;
use \stdClass;

class Experiences extends BaseModel
{
    /**
     * @var string
     */
	protected $table = 'experiences';

    /**
     * @var array
     */
	protected $fillable = ['title', 'cover_image', 'card_image', 'address', 'city_id', 'latitude', 'longitude', 'display_time', 'start_time', 'end_time', 'cost', 'nonveg_preference', 'taxes', 'convenience_fee', 'total_seats', 'avilable_seats', 'action_text', 'tag', 'is_active','is_disabled', 'created_by'];

    /**
     * @return mixed
     */
	public function data()
	{
		return $this->hasMany('App\Models\Privilege\ExpData', 'exp_id')
            ->oldest('sort_order')
            ->oldest('created_at');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function city()
	{
		return $this->belongsTo('App\Models\Privilege\City');
	}

    /**
     * @param int $tickets
     * @param Coupon|null $coupon
     * @return stdClass
     */
	public function estimateCost($tickets = 1, $coupon = null)
    {
		$result = new stdClass();
		$result->cost_for_one = $this->cost;
		$result->tickets = $tickets;
		$result->cost = $this->cost * $tickets;
		$result->convenience_fee = $this->convenience_fee * $tickets;
		$result->taxes = $result->convenience_fee * $this->taxes / 100;
        $result->ori_amount = $result->cost + $result->convenience_fee + $result->taxes;
        $result->coupon_amount = $coupon ? $coupon->amount : 0;
		if ($coupon) {
		    $amount = ($result->cost + $result->convenience_fee + $result->taxes) - $coupon->amount;
		    $result->coupon_id = $coupon->getKey();
            $result->amount = $amount < 1 ? 0 : $amount;
        } else {
            $result->amount = $result->cost + $result->convenience_fee + $result->taxes;
            $result->coupon_id = null;
        }

		return $result;
	}

    /**
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function seats($userId = null)
    {
		ExperiencesSeats::where('created_at', '<', DB::raw('DATE_SUB(NOW() , INTERVAL 10 MINUTE)'))
            ->delete();
		
		if ($userId) {
            return $this->hasMany('App\Models\Privilege\ExperiencesSeats', 'exp_id')->where('user_id', '!=', $userId);
        }

        return $this->hasMany('App\Models\Privilege\ExperiencesSeats', 'exp_id');
	}
	
}