<?php
namespace App\Http\Controllers;
  
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
  
  
class AdminController extends Controller{
  
  
//     public function index(){
//         $Books  = Book::all();
//         return response()->json($Books);
//     }
  
    public function login(Request $request){
  
        $param  = json_decode($request->getContent(),true);
        
        $autharized = false;
        
        $response = array();
        
        if(isset($param['email']) && isset($param['password'])){
        	
	        $admin = Admin::where(array('email'=>$param['email'], 'password'=>$param['password'], 'active'=>1))->first();
	        
	        if($admin){
	        	session_start();
	        	$session_id = session_id();
	        	$response = array(
	        			'APPSESSID'=> session_id(),
	        			'email'=>$admin->email,
	        			'name'=>$admin->name
	        	);
	        	$_SESSION['admin_id'] = $admin->id;
	        	$_SESSION['email'] = $admin->email;
	        	$_SESSION['name'] = $admin->name;
	        	$_SESSION['role'] = $admin->role;
	        	$autharized = true;
	        }
        }
        
        if(!$autharized){
        	return	$this->sendResponse($response,self::UN_AUTHORIZED, 'Invalid parameters');
        }
       	return	$this->sendResponse($response,self::SUCCESS_OK, 'login success');
        
    }
  
    public function logout(Request $request){
    	session_destroy();
        return $this->sendResponse(null,self::REQUEST_ACCEPTED, 'Logout accepted');
  
    }
  
}
?>