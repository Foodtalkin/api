<?php
namespace App\Http\Controllers;
  
use DB;
use App\Models\AccountsAnalytics;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
  
  
class AccountsAnalyticsController extends Controller{
  
  
    public function index(){
    	
    	AccountsAnalytics::updateAnalytics();
//     	DB::connection()->enableQueryLog();
// 		$accounts = array('food-talk-india', 'food-talk-plus', 'instagram', 'twitter', 'mail-chimp');
// 		$result =  array();
    	
        $res = AccountsAnalytics::select('account',DB::raw('group_concat(`count`) as dashboard_count'), DB::raw('group_concat(capture_date) AS dashboard_date') )
        		->where ( 'capture_date','>=',  DB::raw(' ( CURDATE() - INTERVAL 7 DAY )') )
        		->groupBy('account')
        		->get();
//         $que = DB::connection()->getQueryLog();
//         var_dump($que);
        
        return $this->sendResponse($res);
    }
  
    public function get($id){
  
    }
  
    public function create(Request $request){
  
    }
  
    public function delete($id){
    	
    }
  
    public function update(Request $request,$id){

    }
  
}
?>