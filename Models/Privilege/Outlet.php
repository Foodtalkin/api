<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use DB;

class Outlet extends BaseModel
{
    protected $table = 'outlet';
// 	protected $primaryKey = 'id';
    protected $fillable = ['name', 'phone', 'email', 'suggested_dishes', 'address', 'city_id', 'city_zone_id', 'area', 'postcode', 'description', 'resturant_id', 'work_hours', 'pin', 'latitude', 'longitude', 'ft_resturantId', 'disable_reason', 'is_disabled', 'created_by', 'metadata'];
// 	protected $dates = ['start_date'];

    public function offer()
    {
        return $this->belongsToMany('App\Models\Privilege\Offer', 'outlet_offer')->withPivot('id', 'is_disabled');
    }

    public function resturant()
    {
        return $this->belongsTo('App\Models\Privilege\Restaurant');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\Privilege\City');
    }

// 	public function resturant()
// 	{
// 		return $this->belongsTo('App\Models\Privilege\Restaurant');
// 	}

    /**
     * @return mixed
     */
    public function getRatingCount()
    {
        return OfferRedeemed::select(
            DB::raw('count(outlet.id) as total_rating'),
            DB::raw('sum(offer_redeemed.rating) as rating_sum'),
            DB::raw('avg(offer_redeemed.rating) rating_avg')
        )
            ->join('outlet', 'outlet.id', '=', 'offer_redeemed.outlet_id')
            ->where('offer_redeemed.rating', '>', -1)
            ->where('outlet.id', $this->getAttribute('id'))
            ->first();
    }



}