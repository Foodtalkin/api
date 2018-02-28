<?php
namespace App\Console\Commands;


use App\Models\Privilege\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use App\Models\Privilege\PushNotification;
use App\Models\Privilege\ParsePush;


class FixPushNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fix:push:send';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'add user id field in push notification table';
	
	
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
		PushNotification::chunk(50, function ($notifications) {
            foreach ($notifications as $notification) {
                $data = json_decode($notification->push, true);
                if (array_get($data, 'where.userId')) {
                    $notification->fill([
                        'user_id' => array_get($data, 'where.userId')
                    ])->save();
                }

                $this->info(array_get($data, 'where.userId'));
            }
        });

		Subscription::where('created_at', '>', Carbon::today()->subDay(11))
            ->where('subscription_type_id', 1)
            ->chunk(50, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    PushNotification::where([
                        'user_id' => $subscription->user_id,
                        'is_disabled' => '0',
                        'status' => '0'
                    ])->update(['status' => 1]);
                }
            });
	}
}