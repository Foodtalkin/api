<?php
namespace App\Console\Commands;

use App\Models\Privilege\User;
use App\Models\Privilege\UserStatistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use App\Models\Privilege\PushNotification;
use App\Models\Privilege\ParsePush;

class UserStatistics extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:state';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'calculate all user statistics for our dashboard.';
	
	/**
	 * Create a new command instance.
	 *
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
        $totalUser = User::count();

        $userNotSubscribeCount = User::doesntHave('subscription')
            ->count();

        $paidUserCount = User::whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', 1);
        })->count();

        $trailUserCount = User::whereHas('subscription', function ($query) {
                $query->where('subscription.subscription_type_id', '!=', 1)
                    ->where('expiry', '>=', Carbon::today());
            })
            ->doesnthave('subscription', 'and', function ($query) {
                $query->where('subscription.subscription_type_id', 1);
            })
            ->count();

        $trailExpiredUserCount = User::whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', '!=', 1)
                ->where('expiry', '<', Carbon::today());
        })
            ->doesnthave('subscription', 'and', function ($query) {
                $query->where('subscription.subscription_type_id', 1);
            })
            ->count();

        $expiredSubscriptionCount = User::whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', 1)
                ->where('expiry', '<', Carbon::today())
                ->latest();
        })->count();

        UserStatistic::updateOrCreate([
            'totalUser' => $totalUser,
            'userNotSubscribeCount' => $userNotSubscribeCount,
            'paidUserCount' => $paidUserCount,
            'trailUserCount' => $trailUserCount,
            'trailExpiredUserCount' => $trailExpiredUserCount,
            'expiredSubscriptionCount' => $expiredSubscriptionCount,
        ]);

	}
}