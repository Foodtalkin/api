<?php

namespace App\Http\Controllers\Partners;

use App\Models\Privilege\Outlet;

class OutletController extends Controller
{
    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $outlet = Outlet::with('offer')
            ->find($id);

        return $this->sendResponse($outlet);
    }
}