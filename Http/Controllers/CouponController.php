<?php

namespace App\Http\Controllers\Privilege;

use App\Http\Controllers\Controller;
use App\Models\Privilege\Coupon;
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function create(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string|unique:ft_privilege.coupons,code',
            'amount' => 'required|integer',
            'qty' => 'required|integer',
            'created_by' => 'required|integer',
            'expire_at' => 'required|date',
        ]);

        $coupon = Coupon::create([
            'code' => $request->get('code'),
            'amount' => $request->get('amount'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('created_by'),
            'expire_at' => date('Y-m-d', strtotime($request->get('expire_at'))),
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
        $this->validate($request, [
            'code' => 'required|string|unique:ft_privilege.coupons,code,'.$id,
            'amount' => 'required|integer',
            'qty' => 'required|integer',
            'created_by' => 'required|integer',
            'expire_at' => 'required|date',
        ]);

        $coupon = Coupon::findOrFail($id);
        $coupon->update([
            'code' => $request->get('code'),
            'amount' => $request->get('amount'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('created_by'),
            'expire_at' => date('Y-m-d', strtotime($request->get('expire_at'))),
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
            ->where('qty', '>', 0)
            ->where('expire_at', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        if ($coupon) {
            return $this->sendResponse($coupon);
        }

        return $this->sendResponse(null, self::NOT_ACCEPTABLE, 'Coupon code not found or expire');
    }
}