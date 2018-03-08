<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;
use DB;

class Restaurant extends BaseModel
{

    protected $table = 'restaurant';
// 	protected $primaryKey = 'id';
    protected $fillable = ['name', 'cost','description', 'one_liner', 'cover_image', 'card_image', 'primary_cuisine', 'disable_reason', 'is_disabled', 'created_by'];
// 	protected $dates = ['start_date'];


    public function primaryCuisine(){
        return $this->belongsTo('App\Models\Privilege\Cuisine', 'primary_cuisine');
    }

    public function cuisine(){
        return $this->belongsToMany('App\Models\Privilege\Cuisine', 'restaurant_cuisine')->select('cuisine.id', 'cuisine.title')->orderBy('cuisine.title', 'asc');
    }

    public function outlet(){
        return $this->hasMany('App\Models\Privilege\Outlet', 'resturant_id')->orderBy('outlet.name', 'asc');
    }

    public function getRatingCount()
    {
        return OfferRedeemed::select(
            DB::raw('count(outlet.id) as total_rating'),
            DB::raw('sum(offer_redeemed.rating) as rating_sum'),
            DB::raw('avg(offer_redeemed.rating) rating_avg')
        )
            ->join('outlet', 'outlet.id', '=', 'offer_redeemed.outlet_id')
            ->where('offer_redeemed.rating', '>', -1)
            ->where('outlet.resturant_id', $this->getAttribute('id'))
            ->first();
    }

}