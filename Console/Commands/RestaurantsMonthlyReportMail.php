<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Privilege\Offer;
use App\Models\Privilege\ES;
use App\Models\Privilege\Outlet;


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
			
			echo 'CITY : '.$outlet->city->name;
			
		}

	}
	
}