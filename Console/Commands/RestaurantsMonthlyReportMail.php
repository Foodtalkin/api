<?php
namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Models\Privilege\Offer;
use App\Models\Privilege\Outlet;
use App\Models\Privilege\OfferRedeemed;


class RestaurantsMonthlyReportMail extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'restaurants:send-monthly-report {id?}';
	
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
		$id = $this->argument('id');
		$where['is_disabled'] = '0';
		if($id>0){
			$where['id'] = $id;
		}
		
		$outlets = Outlet::select('id', 'name', 'email', 'area', 'city_id',  DB::raw('MONTHNAME(CURRENT_DATE - INTERVAL 1 MONTH) as month')) ->where($where)->get();
		
		foreach ($outlets as $outlet){
			
			$redemptions = OfferRedeemed::select('offer.title', 'offer_redeemed.id', 'offer_redeemed.offers_redeemed', 'offer_redeemed.created_at' )
			->join('offer', 'offer.id', '=', 'offer_redeemed.offer_id')
			->where('offer_redeemed.outlet_id', $outlet->id)
			->where(DB::raw('MONTH(offer_redeemed.created_at)'),'=', DB::raw('MONTH(CURRENT_DATE - INTERVAL 1 MONTH)'))
			->get();
			echo $outlet->name."\n";
			$option['outlet'] =  $outlet;
			$option['redemptions'] = $redemptions;
			$body = Sendgrid::report_tpl($option);
			
// 			$sendgridresponse =	Sendgrid::sendMail(explode(',', $outlet->email), 'Food Talk Monthly Redemption Report', $body);
		}

	}
	
}