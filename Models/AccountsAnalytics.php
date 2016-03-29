<?php namespace App\Models;
  

use DB;
use App\Console\Instagram;
use App\Models\Base\BaseModel;
  
class AccountsAnalytics extends BaseModel
{
	
	 protected $table = 'accounts_analytics';
     protected $fillable = ['account', 'count'];
  
     
	public static function inserORupdate(array $attributes)
	{
		
		DB::insert('insert into accounts_analytics (count, account, capture_date) values (?,?, now()) ON DUPLICATE KEY UPDATE `count`=VALUES(`count`)  ', [$attributes['count'], $attributes['account']] );

	}
	
	public static function updateAnalytics()
	{
		
	        $source_url = 'https://www.facebook.com/foodtalkindia';
    		$rest_url = "http://api.facebook.com/restserver.php?format=json&method=links.getStats&urls=".urlencode($source_url);

    		$json = json_decode(file_get_contents($rest_url),true);
    		$likes  = $json[0]['like_count'];
    		
    		$attributes=array('account'=>'food-talk-india', 'count'=>$likes);    		
    		$Vendors = self::inserORupdate ($attributes);

    		$source_url = 'https://www.facebook.com/foodtalkplus';
    		$rest_url = "http://api.facebook.com/restserver.php?format=json&method=links.getStats&urls=".urlencode($source_url);
    
    		$json = json_decode(file_get_contents($rest_url),true);
    		$likes  = $json[0]['like_count'];
    		
    		$attributes=array('account'=>'food-talk-plus', 'count'=>$likes);
    		$Vendors = self::inserORupdate ($attributes);
    		
    		
    		$json = json_decode(file_get_contents('http://api.twittercounter.com/?twitter_id=1372754112&apikey=26eab4ab5b71bc8892c8c64164294467'),true);
    		$twitterFollowers  = $json['followers_current'];
    		
    		$attributes=array('account'=>'twitter', 'count'=>$twitterFollowers);
    		$Vendors = self::inserORupdate ($attributes);
    		
    		$json = json_decode(file_get_contents('https://us11.api.mailchimp.com/3.0/lists/4b6d309d13?apikey=0f6bb07f190a211c30c7012985cf05f7-us11'),true);
    		$member_count  = $json['stats']['member_count'];
    		
    		$attributes=array('account'=>'mail-chimp', 'count'=>$member_count);
    		$Vendors = self::inserORupdate ($attributes);
    		    		

    		$instagram = new Instagram('4fb089cf7e8b4244a17b8e3080ed4f20');
    		$result = $instagram->getUser(359128846);
    		$res = (array) $result;
    		
    		$attributes=array('account'=>'instagram', 'count'=>$res['data']->counts->followed_by);
    		$Vendors = self::inserORupdate ($attributes);

	}	
     
}
?>