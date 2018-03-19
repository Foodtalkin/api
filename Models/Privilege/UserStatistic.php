<?php namespace App\Models\Privilege;
  
use App\Models\Privilege\Base\BaseModel;

class UserStatistic extends BaseModel
{
    /**
     * @var string
     */
	protected $table = 'user_statistics';

    /**
     * @var array
     */
	protected $fillable = [
	    'totalUser',
        'userNotSubscribeCount',
        'paidUserCount',
        'trailUserCount',
        'trailExpiredUserCount',
        'expiredSubscriptionCount'
    ];
}