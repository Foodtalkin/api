<?php

namespace App\Http\Controllers\Partners;

use App\Http\Controllers\Controller;
use App\Models\Privilege\OfferRedeemed;
use App\Models\Privilege\Outlet;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function offers(Request $request, $id)
    {
        $arr = $request->getRawPost(true);
        $outlet = Outlet::with('offer')
            ->find($id);
        if (array_get($arr, 'start_date') && array_get($arr, 'end_date')) {
            $startDay = Carbon::parse(array_get($arr, 'start_date'));
            $endDay = Carbon::parse(array_get($arr, 'end_date'));
        } else {
            $startDay = Carbon::today()->subMonth(1);
            $endDay = Carbon::today();
        }

        $result = [
            'overall' => [],
            'users' => OfferRedeemed::selectRaw('user.name as user, user.phone, user.email, offer_redeemed.id, offer.title, offer_redeemed.offers_redeemed, offer_redeemed.created_at')
                ->join('offer', 'offer.id', '=', 'offer_redeemed.offer_id')
                ->leftJoin('user', 'user.id', '=', 'offer_redeemed.user_id')
                ->where('offer_redeemed.outlet_id', '=', $outlet->getKey())
                ->whereBetween('offer_redeemed.created_at', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
                ->oldest('offer_redeemed.created_at')
                ->limit(500)
                ->get()
        ];

        foreach ($outlet->offer as $offer) {
            $fullRecords = [];
            $records = OfferRedeemed::select(DB::raw('DATE_FORMAT(offer_redeemed.created_at,"%Y-%m-%d") as date'), DB::raw('count(offer_redeemed.id) as count'))
                ->where('outlet_id', $outlet->getKey())
                ->where('offer_id', $offer->getKey())
                ->whereBetween('created_at', [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')])
                ->groupBy('date')
                ->get();

            $diff = $startDay->diff($endDay);
            for ($i = 0; $i < $diff->days; $i++) {
                $temp = clone $startDay;
                $newDate = $temp->addDay($i);
                $first = $records->first(function ($key, $redeem) use($newDate) {
                    return $redeem->date == $newDate->format('Y-m-d');
                });

                if ($first)  {
                    $fullRecords[] = [
                        'date' => $newDate->format('Y-m-d'),
                        'count' => $first->count,
                    ];
                } else {
                    $fullRecords[] = [
                        'date' => $newDate->format('Y-m-d'),
                        'count' => 0,
                    ];
                }
            }

            $result['datewise'][$offer->id] = $fullRecords;
        }


        return $this->sendResponse($result);
    }
}