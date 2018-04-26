<?php

namespace App\Http\Controllers\Partners;

use App\Models\Privilege\OfferRedeemed;
use App\Models\Privilege\Outlet;
use Carbon\Carbon;

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

        $outlet->overallRedeem = OfferRedeemed::where('outlet_id', $outlet->getKey())
            ->count();

        $outlet->currentMonthRedeem = OfferRedeemed::where('outlet_id', $outlet->getKey())
            ->whereBetween('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::today()->format('Y-m-d')])
            ->count();

        return $this->sendResponse($outlet);
    }
}