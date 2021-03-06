<?php


namespace App\Console;

use App\Models\AccountsAnalytics;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    		'App\Console\Commands\UploadEmailsToMailerList',
    		'App\Console\Commands\UploadOffersToElastic',
    		'App\Console\Commands\FixPushNotification',
    		'App\Console\Commands\SendPushNotification',
    		'App\Console\Commands\RestaurantsMonthlyReportMail',
    		'App\Console\Commands\UserStatistics',

        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	$schedule->call(function () {
			AccountsAnalytics::updateAnalytics();
    	})
    	->dailyAt('23:30');
//     	->everyMinute();
    }
}
