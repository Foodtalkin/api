<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
	
	
	return response( '{"message":"welcom to '.$_SERVER['HTTP_HOST'].'"}', 200, ['Content-type'=>'application/json']);
});


// 	$app->group(['prefix' => 'api/v1','namespace' => 'App\Http\Controllers'], function($app)
// 	{
// 		$app->get('/book','BookController@index');
	
// 		$app->get('book/{id}','BookController@getbook');
	
// 		$app->post('book','BookController@createBook');
	
// 		$app->put('book/{id}','BookController@updateBook');
	
// 		$app->delete('book/{id}','BookController@deleteBook');
// 	});


	$app->group(['namespace' => 'App\Http\Controllers\Privilege', 'middleware' => 'privilegeuser' ], function($app)
	{

		$app->get('outlet/{outlet_id}/offer/{offer_id}', [ 'uses' =>'OfferController@offerWithOutlet']);
		$app->get('checkuser/{phone}', [ 'uses' =>'UserController@checkUser']);
		$app->get('restaurant/{id}', [ 'uses' =>'RestaurantController@get']);
		$app->get('restaurant/outlets/{id}', [ 'uses' =>'RestaurantController@outlets']);
		$app->get('outlet/{id}', [ 'uses' =>'OutletController@get']);
		$app->get('outletoffer/{outlet_id}', [ 'uses' =>'OfferController@outletOffer']);
		$app->get('offers', [ 'uses' =>'OfferController@listAll']);
		
		$app->post('user/event', [ 'uses' =>'UserController@event']);
		
		$app->get('offer_types', [ 'uses' =>'OfferController@getAll']);
		
		$app->get('search/{searchText}', [ 'uses' =>'OfferController@search']);
		$app->get('offer/{id}', [ 'uses' =>'OfferController@get']);
		
		
		$app->post('refreshsession', [ 'uses' =>'UserController@refreshSession']);
		$app->post('getotp', [ 'uses' =>'UserController@getOTP']);
		$app->get('resendotp/{phone}', [ 'uses' =>'UserController@resendOTP']);
		
		$app->get('paymentmode', [ 'uses' =>'UserController@paymentMode']);
		
		$app->post('userlogin', [ 'uses' =>'UserController@login']);
		$app->get('avilablesubscription', [ 'uses' =>'UserController@avilableSubscription']);
		
		
		$app->get('cuisine', [ 'uses' =>'RestaurantController@cuisine']);

		$app->get('profile', [ 'uses' =>'UserController@profile']);
		
// 		$app->group(['namespace' => 'App\Http\Controllers\Privilege'],function($app){
			
			$app->put('user', [ 'middleware' => 'athuprivilage', 'uses' =>'UserController@update']);
			
			$app->post('subscription', [ 'middleware' => 'athuprivilage', 'uses' =>'UserController@subscription']);
			
			$app->post('subscriptionPayment', [ 'middleware' => 'athuprivilage', 'uses' =>'UserController@subscriptionPayment']);
			
			
			$app->post('redeem', [ 'middleware' => 'athuprivilage', 'uses' =>'OfferController@redeem']);
			$app->get('redeemhistory', [ 'middleware' => 'athuprivilage', 'uses' =>'OfferController@redeemHistory']);
			
			$app->post('bookmark/{id}', [ 'middleware' => 'athuprivilage', 'uses' =>'OfferController@bookmark']);
			$app->delete('bookmark/{id}', [ 'middleware' => 'athuprivilage', 'uses' =>'OfferController@removeBookmark']);
			$app->get('bookmark', [ 'middleware' => 'athuprivilage', 'uses' =>'OfferController@listBookmark']);
				
			$app->post('webhook/instamojo', [ 'uses' =>'UserController@webhookInstamojo']);
			
// 		});
		
	});
	
	$app->group(['namespace' => 'App\Http\Controllers\Privilege', 
// 			'middleware' => 'privilegeuser'
// 						'middleware' => 'auth', 
			'prefix' => 'privilege' 
	], function($app)
	{
		
		
		
		$app->get('analytics/redeemptions', [ 'uses' =>'AnalyticsController@redeemptions']);
		$app->get('analytics/signups', [ 'uses' =>'AnalyticsController@signups']);
		$app->get('analytics/purchases', [ 'uses' =>'AnalyticsController@purchases']);
		
		
		$app->get('analytics/user/{days}', [ 'uses' =>'AnalyticsController@users']);
		$app->get('analytics/user', [ 'uses' =>'AnalyticsController@users']);
		$app->get('analytics/redemption', [ 'uses' =>'AnalyticsController@offers']);
		$app->get('analytics/redemption/{days}', [ 'uses' =>'AnalyticsController@offers']);
		
		$app->get('analytics/restaurants', [ 'uses' =>'AnalyticsController@restaurants']);
		$app->get('analytics/restaurants/{days}/{top}', [ 'uses' =>'AnalyticsController@restaurants']);
		$app->get('analytics/restaurants/{days}', [ 'uses' =>'AnalyticsController@restaurants']);

		$app->get('user/event', [ 'uses' =>'UserController@allevent']);
		
		$app->get('log/{entity}/id/{id}','DBLogController@get');
		$app->get('log/{entity}','DBLogController@get');
		
		$app->get('user', [ 'uses' =>'OutletController@get']);
		$app->get('cuisine', [ 'uses' =>'RestaurantController@allCuisine']);
		
		$app->get('restaurant', [ 'uses' =>'RestaurantController@listresto']);
		$app->get('restaurant/{id}', [ 'uses' =>'RestaurantController@get']);
		$app->post('restaurant', [ 'uses' =>'RestaurantController@create']);
		$app->put('restaurant/{id}', [ 'uses' =>'RestaurantController@update']);
		$app->delete('restaurant/{id}', [ 'uses' =>'RestaurantController@delete']);
		
		$app->post('restaurant/{id}/cuisine', [ 'uses' =>'RestaurantController@addCuisine']);
		$app->delete('restaurant/{id}/cuisine/{cuisineId}', [ 'uses' =>'RestaurantController@removeCuisine']);
		
		
		$app->get('offer/{id}', [ 'uses' =>'OfferController@get']);
		$app->get('offer', [ 'uses' =>'OfferController@getAll']);
		$app->post('offer', [ 'uses' =>'OfferController@create']);
		$app->put('offer/{id}', [ 'uses' =>'OfferController@update']);
		$app->delete('offer/{id}', [ 'uses' =>'OfferController@delete']);
		
		$app->get('outlet', [ 'uses' =>'OutletController@getAll']);
		$app->get('outlet/{id}', [ 'uses' =>'OutletController@get']);
		$app->post('outlet', [ 'uses' =>'OutletController@create']);
		$app->put('outlet/{id}', [ 'uses' =>'OutletController@update']);
		$app->delete('outlet/{id}', [ 'uses' =>'OutletController@delete']);
		
		$app->get('outlet/{id}/image', [ 'uses' =>'OutletController@getAllImages']);
		$app->post('outlet/{id}/image', [ 'uses' =>'OutletController@addImages']);
		$app->delete('outlet/{id}/image/{imageId}', [ 'uses' =>'OutletController@deleteImage']);

		
		
		
		$app->get('outlet-offer', [ 'uses' =>'OutletOfferController@listAll']);
		$app->get('outlet-offer/{id}', [ 'uses' =>'OutletOfferController@get']);
		$app->post('outlet-offer', [ 'uses' =>'OutletOfferController@saveOutletOffer']);
		$app->put('outlet-offer/{id}', [ 'uses' =>'OutletOfferController@saveOutletOffer']);
		$app->delete('outlet-offer/{id}', [ 'uses' =>'OutletOfferController@disable']);
		
		
	});
	
	
// 	App\Http\Controllers\Privilege
	
	
	$app->group(['prefix' => '/' ,'namespace' => 'App\Http\Controllers'], function($app)
	{
		$app->post('login', [ 'uses' =>'AdminController@login']);
		$app->delete('logout', ['middleware' => 'auth', 'uses' =>'AdminController@logout']);
	});
	
	// list all users
	$app->get('user','UserController@listAll');
	$app->get('user/{for:onapp|nonapp}','UserController@listAll');
	
	
	$app->get('user/city/{city}','UserController@listAllWithCity');
	$app->get('user/city','UserController@listAllWithCity');
	
	
	$app->get('city','CityController@listAll');
	
	
	
	$app->post('contact','ContactController@create');
	$app->put('contact/{id}','ContactController@update');
	
	
	// get user's info
	$app->get('user/{id}','UserController@get');
	
	// get events of user's and its info
	$app->get('user/{id}/{with:events}','UserController@get');
	
	
	// creates a new user
	$app->post('user','UserController@create');
	
	$app->post('emailavilability','UserController@checkEmail');
	
	// update a user
	$app->put('user/{id}','UserController@update');
	
	// delete a user
	$app->delete('user/{id}','UserController@delete');
	
	// update a user
	$app->post('user/{id}/{ptype:participation|rsvp}','UserController@participation');

	$app->get('getfeeds','EventController@getMobileAppFeeds');
	
	
// as per himanshu	
// 	$app->get('{type:event|contest}', [ 'uses' =>'EventController@listAll']);
	$app->get('{type:event|contest}/{status:upcomming|ongoing}', [ 'uses' =>'EventController@listAll']);
	$app->get("{type:event|contest}/{id}",[ 'uses' =>'EventController@get', 'as'=>'ssssss']);
	$app->get("{type:event|contest}/{id}/{with:participants}",'EventController@get');
	$app->get('sub_category','SubCategoryController@listAll');
	$app->get('sub_category/{id}','SubCategoryController@get');
	$app->get('sub_category/{id}/{with:events}','SubCategoryController@get');
	

	$app->post('{type:event|contest}/{id}/addtags',[ 'uses' =>'EventController@addTags']);
	
	
	$app->post('transaction/{method:instamojo}',[ 'uses' =>'TransactionController@create']);
	$app->post('transaction/{method:instamojo}/{id}',[ 'uses' =>'TransactionController@create']);
	
	
	$app->group([
// 					'middleware' => 'auth', 
					'prefix' => 'api',
					'namespace' => 'App\Http\Controllers'
				], function($app)
				{
					$app->get('contact','ContactController@listAll');
					$app->get('contact/{id}','ContactController@get');
					
					$app->get('search/user/tags/{tags}','UserController@tag');
					$app->get('search/user/{text}/tags/{tags}','UserController@search');
					$app->get('search/user/{text}','UserController@search');
					
					$app->get('transaction',[ 'uses' =>'TransactionController@listAll']);
					
					$app->get('partners', [ 'uses' =>'PartnersController@listAll']);
					$app->post('partners', [ 'uses' =>'PartnersController@create']);
					$app->put('partners/{id}', [ 'uses' =>'PartnersController@update']);
					$app->delete('partners/{id}', [ 'uses' =>'PartnersController@delete']);
					$app->get('partners/{id}', [ 'uses' =>'PartnersController@get']);
					$app->get('partners/{id}/{with:events}',[ 'uses' =>'PartnersController@get']);
					
					$app->post('{type:event}/{id}/partners',[ 'uses' =>'EventController@addPartners']);
					$app->delete('{type:event}/{id}/partners/{partners_id}','EventController@deletePartners');
						
					
// 					event|contest api
					$app->get('{type:event|contest}', [ 'uses' =>'EventController@listAll']);
					$app->get('{type:event|contest}/{status:upcomming|ongoing|disabled|pending|active|past}', [ 'uses' =>'EventController@listAll']);
					$app->get("{type:event|contest}/{id}", [ 'uses' =>'EventController@get', 'as'=>'ssssss']);
					$app->get("{type:event|contest}/{id}/{with:participants}",'EventController@get');
			
					
					$app->post('{type:event|contest}','EventController@create');
				
					$app->put('{type:event|contest}/{id}','EventController@update');
				
					$app->delete('{type:event|contest}/{id}','EventController@delete');
					
					$app->post('{type:event}/{id}/vendors','EventController@addVendors');
					$app->delete('{type:event}/{id}/vendors/{vendors_id}','EventController@deleteVendors');
					
//					vendors category
					$app->get('{type:vendor|media|bloggers|influencer}/category', [ 'uses' =>'VendorsCategoryController@listAll']);
					$app->post('{type:vendor|media|bloggers|influencer}/category', [ 'uses' =>'VendorsCategoryController@create']);
						
					$app->get('{type:vendor|media|bloggers|influencer}/category/{id}', [ 'uses' =>'VendorsCategoryController@get']);
					
					$app->get('{type:vendor|media|bloggers|influencer}/category/{id}/{with:vendors}', [ 'uses' =>'VendorsCategoryController@get']);
						
					$app->put('{type:vendor|media|bloggers|influencer}/category/{id}', [ 'uses' =>'VendorsCategoryController@update']);
					$app->delete('{type:vendor|media|bloggers|influencer}/category/{id}', [ 'uses' =>'VendorsCategoryController@delete']);
					
// 					vendor's api
					$app->get('{type:vendor|media|bloggers|influencer}', [ 'uses' =>'VendorsController@listAll']);
					$app->post('{type:vendor|media|bloggers|influencer}', [ 'uses' =>'VendorsController@create']);						
					
					$app->get('{type:vendor|media|bloggers|influencer}/{id}', [ 'uses' =>'VendorsController@get']);
					$app->put('{type:vendor|media|bloggers|influencer}/{id}', [ 'uses' =>'VendorsController@update']);
					$app->delete('{type:vendor|media|bloggers|influencer}/{id}', [ 'uses' =>'VendorsController@delete']);
						
					
// 					sub_category api
					$app->get('sub_category','SubCategoryController@listAll');
					
					$app->post('sub_category','SubCategoryController@create');
					$app->get('sub_category/{id}','SubCategoryController@get');
					$app->get('sub_category/{id}/{with:events}','SubCategoryController@get');
					
					$app->put('sub_category/{id}','SubCategoryController@update');
					$app->delete('sub_category/{id}','SubCategoryController@delete');
					
					$app->get('dashboard/batcave','AccountsAnalyticsController@index');
					
					
				}
	);
	
	
// $app->get('/', function () use ($app) {
//     return $app->welcome();
// });

// 	$app->get('/hello', function () use ($app) {
// 		return $app->welcome();
// 	});