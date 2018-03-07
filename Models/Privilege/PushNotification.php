<?php namespace App\Models\Privilege;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\Base\BaseModel;

class PushNotification extends BaseModel
{
	protected $table = 'push_notification';
	// 	protected $primaryKey = 'id';
	protected $fillable = ['user_id', 'push_time', 'title', 'push', 'metadata', 'status', 'is_disabled', 'created_by'];
	// 	protected $dates = ['start_date'];
	
	
	public static function trialPush($user_id){
		
// 		$attributes['push'] = json_encode($attributes['push']);
		$startTrial = new self();
		$startTrial->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"Your 7 day free trial has started! Where is your first meal going to be?","badge":"Increment"}}';
		$startTrial->save();
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"6 days left before your Trial expires!","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+1 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
		
// days left	6 5 4 3 2 1 0 -1
// 	        	1 2 3 4 5 6 7  8
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"3 days left before your Trial expires!","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+4 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
		
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"2 days left before your Trial expires!","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+5 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"LAST DAY of your free trial Trial! Buy your annual membership to continue.","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+6 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"Your Trial has expired. Buy your annual membership to continue.","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+7 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
		
		$day2 = new self();
        $day2->user_id = $user_id;
		$day2->push = '{"where":{"userId":"'.$user_id.'"},"data":{"alert":"365 days of great food and savings is a tap away. Buy your annual membership to continue.","badge":"Increment"}}';
		$pushTime = Date('y-m-d 16:00:00', strtotime("+8 days"));
		$day2->push_time = $pushTime ;
		$day2->save();
	}
	
	
	
}