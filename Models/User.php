<?php namespace App\Models;
  
// use Illuminate\Database\Eloquent\Model;
use App\Models\Base\BaseModel;
use DB;

class User extends BaseModel
{
	protected $table = 'user'; 
// 	protected $primaryKey = 'uid';
	protected $fillable = ['name', 'gender', 'email', 'dob', 'city', 'facebook_id', 'contact', 'instagram_handle','facebook_handle', 'twitter_handle', 'address', 'metadata'];
// 	protected $hidden = [ 'password' ];
	
	
// 	public function particiption()
// 	{
// 		return $this->hasMany('App\Models\EventParticipant');
// 	}
	
	public function events()
	{
		
// 		$this->belongsToMany($related)
		return $this->belongsToMany('App\Models\Events', 'event_participant')
// 		->wherePivot('email', '=', 'email@email.com')
		->withPivot('email', 'contact', 'payment', 'quantity', 'response', 'created_at');
	}
	
	
	public function appInfo()
	{
		$sql = 'select
u.userName as user_name, u.createDate as joing_date, 
u.facebookId as facefook_id,
(SELECT COUNT(1) FROM `foodtalk`.post p WHERE p.userId = u.id AND p.isDisabled = 0 ) as post_count,
(SELECT COUNT(1) FROM `foodtalk`.checkins ck WHERE ck.userId = u.id ) as checkin_count,
(SELECT COUNT(1) FROM `foodtalk`.comment c WHERE c.userId = u.id AND c.isDisabled = 0 ) as comment_count,

(SELECT COUNT(1) FROM `foodtalk`.`like` l WHERE l.userId = u.id AND l.isDisabled = 0 ) as like_count,
(SELECT COUNT(1) FROM `foodtalk`.follower f WHERE f.followerUserId = u.id ) as following_count,
(SELECT COUNT(1) FROM `foodtalk`.follower f2 WHERE f2.followedUserId = u.id ) as followers_count,
(SELECT MAX(createDate) FROM `foodtalk`.checkins ck1 WHERE ck1.userId = u.id ) as last_checkin,
(SELECT MAX(createDate) FROM `foodtalk`.`like` l2 WHERE l2.userId = u.id AND l2.isDisabled = 0 ) as last_like,
(SELECT MAX(createDate) FROM `foodtalk`.comment c2 WHERE c2.userId = u.id AND c2.isDisabled = 0 ) as last_comment,
(SELECT MAX(createDate) FROM `foodtalk`.post p2 WHERE p2.userId = u.id AND p2.isDisabled = 0 ) as last_post,
(SELECT MAX(createDate) FROM `foodtalk`.follower f3 WHERE f3.followerUserId = u.id ) as last_follow,
(SELECT COUNT(1) FROM `foodtalk`.bookmark b WHERE b.userId = u.id AND b.isDisabled = 0 ) as bookmark_count,
(SELECT MAX(createDate) FROM `foodtalk`.bookmark b2 WHERE b2.userId = u.id AND b2.isDisabled = 0 ) as last_bookmark,
a.updateDate as last_activity
FROM `foodtalk`.`user` u 
LEFT JOIN `foodtalk`.activityScore a on u.facebookId = a.facefookId
where u.facebookId = "'.$this->facebook_id.'"';
		$result = DB::select(DB::raw($sql));
		if(empty($result))
			return null;
		else
			return $result[0];
	}
	
}
?>