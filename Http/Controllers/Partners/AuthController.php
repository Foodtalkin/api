<?php

namespace App\Http\Controllers\Partners;

use App\Models\Privilege\Restaurant;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        $arr = $request->getRawPost(true);

        $restaurant = Restaurant::where('partner_pin', array_get($arr, 'partner_pin'))
            ->first();

        if ($restaurant && env('PARTNER_PASSWORD') == array_get($arr, 'password')) {
            session_start();
            $session_id = session_id();
            $response = [
                'APPSESSID'=> $session_id,
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'partner_pin' => $restaurant->partner_pin
            ];
            $_SESSION['restaurant_id'] = $restaurant->id;
            $_SESSION['partner_pin'] = $restaurant->partner_pin;
            $_SESSION['name'] = $restaurant->name;

            return $this->sendResponse($response,self::SUCCESS_OK, 'login success');
        }

        return	$this->sendResponse([],self::UN_AUTHORIZED, 'Invalid parameters');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        session_destroy();

        return $this->sendResponse(null,self::REQUEST_ACCEPTED, 'Logout accepted');
    }
}
