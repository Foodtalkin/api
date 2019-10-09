<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use DB;
use App\Models\Privilege\PushNotification;
use App\Models\Privilege\ParsePush;


class SendPushNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'push:send';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'send all pending push notification to FoodTalkPrivilage members ';
	
	
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
		$result = collect(); //PushNotification::where(array('is_disabled'=>'0', 'status'=>'0'))->where('push_time', '<', DB::raw('now()'))->get();
		if(!empty($result)){
			foreach ($result as $push){
                $data = json_decode($push->push, true);

                if ($push->title) {
                    $iosPush = $data;
                    $androidPush = $data;
                    $title = $push->title;

                    /**
                     * send ios notification
                     */
                    $iosPush['data']['alert'] = [
                        'title' => $title,
                        'body' => array_get($iosPush, 'data.alert')
                    ];
                    $iosPush = $this->getWhereForDevice($iosPush, 'ios');
                    ParsePush::send($iosPush);
                    $this->info('ios send');

                    /**
                     * send android notification
                     */
                    $androidPush['data'] = array_merge($androidPush['data'], [
                        'title' => $title,
                    ]);

                    $androidPush = $this->getWhereForDevice($androidPush, 'android');

                    ParsePush::send($androidPush);
                    $this->info('android send');
                } else {
                    $response = ParsePush::send($data);
                }

				$push->status = '1';
				$push->save();
			}
		}
	}

    /**
     * @param $data
     * @param $device
     * @return mixed
     */
    protected function getWhereForDevice($data, $device)
    {
        if (array_get($data, 'where')) {
            $data['where']['deviceType'] = [
                '$in' => [$device]
            ];
        } else {
            $data['where'] = [
                'deviceType'  => [
                    '$in' => [$device]
                ]
            ];
        }

        return $data;
    }
}