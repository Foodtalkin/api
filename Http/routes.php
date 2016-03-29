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

	
	$app->group(['prefix' => '/' ,'namespace' => 'App\Http\Controllers'], function($app)
	{
		$app->post('login', [ 'uses' =>'AdminController@login']);
		$app->delete('logout', ['middleware' => 'auth', 'uses' =>'AdminController@logout']);
	});
	
	
	// list all users
	$app->get('user','UserController@listAll');
	
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
					'middleware' => 'auth', 
					'prefix' => 'api',
					'namespace' => 'App\Http\Controllers'
				], function($app)
				{
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