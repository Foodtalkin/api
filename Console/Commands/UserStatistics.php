<?php
namespace App\Console\Commands;

use App\Models\Privilege\User;
use App\Models\Privilege\UserStatistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

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

        $state = UserStatistic::first();

        if ($state) {
            $state->fill([
                'totalUser' => $totalUser,
                'userNotSubscribeCount' => $userNotSubscribeCount,
                'paidUserCount' => $paidUserCount,
                'trailUserCount' => $trailUserCount,
                'trailExpiredUserCount' => $trailExpiredUserCount,
                'expiredSubscriptionCount' => $expiredSubscriptionCount,
                'count' => rand(0, 99999),
            ])->save();
        } else {
            UserStatistic::create([
                'totalUser' => $totalUser,
                'userNotSubscribeCount' => $userNotSubscribeCount,
                'paidUserCount' => $paidUserCount,
                'trailUserCount' => $trailUserCount,
                'trailExpiredUserCount' => $trailExpiredUserCount,
                'expiredSubscriptionCount' => $expiredSubscriptionCount,
                'count' => 1,
            ]);
        }

        $this->createCSVReport();
    }

    /**
     * generate csv data for different type of user
     */
    protected function createCSVReport()
    {
        // paid users csv
        $paidUsers = $this->getBaseQuery()->whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', 1);
        })->get();

        $this->generateUserCsv($paidUsers, 'paid_user.csv');

        // active trials users csv
        $activeTrials = $this->getBaseQuery()->whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', '!=', 1)
                ->where('expiry', '>=', Carbon::today());
        })
            ->doesnthave('subscription', 'and', function ($query) {
                $query->where('subscription.subscription_type_id', 1);
            })
            ->get();
        $this->generateUserCsv($activeTrials, 'active_trials.csv');

        // Only Signed up User csv
        $onlySignedUp = $this->getBaseQuery()->doesntHave('subscription')
            ->get();
        $this->generateUserCsv($onlySignedUp, 'only_signed_up.csv');

        // Trail Expired Users csv
        $trailExpired = $this->getBaseQuery()->whereHas('subscription', function ($query) {
            $query->where('subscription.subscription_type_id', '!=', 1)
                ->where('expiry', '<', Carbon::today());
        })
            ->doesnthave('subscription', 'and', function ($query) {
                $query->where('subscription.subscription_type_id', 1);
            })
            ->get();

        $this->generateUserCsv($trailExpired, 'trail_expired.csv');

        // all users csv
        $allUsers = $this->getBaseQuery()
            ->get();
        $this->generateUserCsv($allUsers, 'all_user.csv');
    }

    /**
     * @return mixed
     */
    protected function getBaseQuery()
    {
        return User::select('user.id', 'user.name', 'email', 'phone', 'gender', DB::raw('city.name as city'), 'dob', 'saving')
            ->join('city', 'city.id', '=', 'user.city_id');
    }

    /**
     * @param $result
     * @param $fileName
     */
    protected function generateUserCsv($result, $fileName)
    {
        @unlink(storage_path($fileName));

        $allRecords = array_merge([
            ['id', 'Name', 'Email', 'Phone', 'Gender', 'City', 'DOB', 'Saving'], [],
        ], $result->toArray());

        $fp = fopen(storage_path($fileName), 'w');
        foreach ($allRecords as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }
}