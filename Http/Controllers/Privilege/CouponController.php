<?php

namespace App\Http\Controllers\Privilege;

use App\Http\Controllers\Controller;
use App\Models\Privilege\Coupon;
use App\Models\Privilege\SubscriptionType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAll()
    {
        $coupons = Coupon::paginate(Coupon::PAGE_SIZE);

        return $this->sendResponse($coupons);
	}

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function show($id)
    {
        $result = Coupon::find($id);

        $result['users'] = Coupon::select('user.id', 'user.name', 'user.phone', 'paytm_order_status.created_at')
            ->join('paytm_order', 'coupons.id', '=', 'paytm_order.coupon_id')
            ->join('paytm_order_status', 'paytm_order.id', '=', 'paytm_order_status.paytm_order_id')
            ->join('user', 'paytm_order.user_id', '=', 'user.id')
            ->where('paytm_order_status.payment_status', 'TXN_SUCCESS')
            ->where('coupons.id', $id)
            ->get();

        return $this->sendResponse($result);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function create(Request $request)
    {
        $request->headers->add(['Accept' => 'application/json', 'Content-Type' => 'application/json']);
        $this->validate($request, [
            'code' => 'required|string|unique:ft_privilege.coupons,code',
            'description' => 'required|string',
            'discount' => 'required|integer',
            'duration' => 'required|integer',
            'qty' => 'required|integer',
            'expire_at' => 'required|date',
        ]);

        $coupon = Coupon::create([
            'code' => $request->code,
            'description' => $request->description,
            'discount' => $request->discount,
            'duration' => $request->duration,
            'qty' => $request->qty,
            'expire_at' => Carbon::parse($request->expire_at)->format('Y-m-d'),
            'note' => $request->note,
        ]);

        return $this->sendResponse($coupon);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $request->headers->add(['Accept' => 'application/json', 'Content-Type' => 'application/json']);
        $this->validate($request, [
            'code' => 'required|string|unique:ft_privilege.coupons,code,'.$id,
            'description' => 'required|string',
            'discount' => 'required|integer',
            'duration' => 'required|integer',
            'qty' => 'required|integer',
            'expire_at' => 'required|date',
        ]);

        $coupon = Coupon::findOrFail($id);
        $coupon->update([
            'code' => $request->code,
            'description' => $request->description,
            'discount' => $request->discount,
            'duration' => $request->duration,
            'qty' => $request->qty,
            'expire_at' => Carbon::parse($request->expire_at)->format('Y-m-d'),
            'note' => $request->note,
        ]);

        return $this->sendResponse($coupon);
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        $coupon = Coupon::findOrFail($id);

        if ($coupon) {
            $coupon->fill([
                'is_active' => false,
                'is_disabled' => true,
            ])->save();

            return $this->sendResponse(true, self::REQUEST_ACCEPTED, 'Coupon Disabled');
        } else {
            return $this->sendResponse (null);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateCode(Request $request)
    {
        $coupon = Coupon::where('code', $request->get('code'))
            ->where('qty', '>', 0)
            ->where('is_active', true)
            ->where('is_disabled', false)
            ->where('expire_at', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        if ($coupon) {
            // @todo type must be dynamic
            $type = SubscriptionType::where('id', '=',1)->first();
            $estimation = $coupon->estimateSubscription($type);

            return $this->sendResponse($estimation);
        }

        return $this->sendResponse(null, self::NOT_ACCEPTABLE, 'Coupon code is invalid or expired');
    }
}