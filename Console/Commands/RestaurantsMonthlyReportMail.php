<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Privilege\Offer;
use App\Models\Privilege\ES;
use App\Models\Privilege\Outlet;
use App\Models\Privilege\OfferRedeemed;


class RestaurantsMonthlyReportMail extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'restaurants:send-monthly-report';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'monthly report email to restaurants for offers redeemptions ';
	
	
	/**
	 * Create a new command instance.
	 *
	 * @param  DripEmailer  $drip
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$outlets = Outlet::select('id', 'name', 'area', 'email', 'city_id', 'postcode')->where('is_disabled','0')->get();
		
		foreach ($outlets as $outlet){
			
			$redemptions = OfferRedeemed::select('offer.title', 'offer_redeemed.id', 'offer_redeemed.offers_redeemed', 'offer_redeemed.created_at' )
			->join('offer', 'offer.id', '=', 'offer_redeemed.offer_id')
			->where('outlet_id', $outlet->id)
			->get();
			
			
			echo $redemptions->id.' | '.$redemptions->title.' | '.$redemptions->offers_redeemed.' | '.date_format($redemptions->created_at, 'D M Y').' | '.date_format($redemptions->created_at, 'h:i A')."\n";
			
		}

	}
	
}