<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Privilege\Offer;
use App\Models\Privilege\ES;


class UploadOffersToElastic extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'upload:offers';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Upload/index offers to Elastic search';
	
	
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
		$offers = Offer::getAllOffers(['paginate'=>'no']);

		$url = '/ft_privilege';
		$result = ES::request('', $url, 'DELETE');
		if(isset($result->errors) && $result->errors)
			echo json_encode($result);
		
		ob_start();
		
		foreach ($offers as $key => $offer){
			echo '{ "index" : { "_index" : "ft_privilege", "_type" : "restaurant", "_id" : "'.$offer->rid.'" } }'."\n";
			echo $offer->tojson();
			echo "\n";
		}
		
		$content = ob_get_contents();
		ob_end_clean();

		$url = '/ft_privilege/_bulk';
		
		$result = ES::request($content, $url, 'POST');
		if($result->errors)
			echo json_encode($result);
	}
	
}