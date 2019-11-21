<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramController extends Controller
{
    public function store(Request $request)
    {
    	$attributes = $this->getResponseArr ( $request );
    	$url = array_get($attributes, 'url');
        $user = str_replace('https://www.instagram.com/', '', $url);
    	$username = explode('/', $user);
        $response = @file_get_contents( "https://www.instagram.com/$username[0]/?__a=1" );

        $content = [];

        if ( $response !== false ) {
            $data = json_decode( $response, true );
            if ( $data !== null ) {
                $content = [
                    'followers' => array_get($data, 'graphql.user.edge_followed_by.count'),
                    'following' => array_get($data, 'graphql.user.edge_follow.count')
                ];
            }
        } else {
            $content = [
                'followers' => 0,
                'following' => 0
            ];
        }

    	return $this->sendResponse ( $content );
    }
}
