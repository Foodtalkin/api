<?php

namespace App\Http\Controllers\Partners;

use App\Models\Privilege\Restaurant;

class RestaurantController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $id = array_get($_SESSION, 'restaurant_id');
        if (! $id) {
            return $this->sendResponse([], self::NO_ENTITY);
        }

        $result = Restaurant::with('primaryCuisine', 'cuisine', 'outlet')
            ->find($id);

        return $this->sendResponse($result);
    }
}