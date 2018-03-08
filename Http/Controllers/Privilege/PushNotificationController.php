<?php

namespace App\Http\Controllers\Privilege;

use App\Models\Privilege\ParsePush;
use App\Models\Privilege\User;
use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\PushNotification;
// use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller {

    public function getAll(Request $request) {
        $result = PushNotification::latest()
            ->paginate($this->pageSize);

        return $this->sendResponse($result, self::SUCCESS_OK);
    }

    public function get(Request $request, $id) {
        $result = PushNotification::find ( $id );
        return $this->sendResponse ( $result);
    }

    public function create(Request $request) {
        $attributes = $request->getRawPost(true);
        $attributes['push'] = json_encode($attributes['push']);

        if(isset($attributes['status']))
            unset($attributes['status']);
        $result = PushNotification::create ( $attributes );
        return $this->sendResponse ( $result);
    }

    public function update(Request $request, $id) {

        $attributes = $request->getRawPost(true);
        $result= PushNotification::find ( $id );
        $attributes['push'] = json_encode($attributes['push']);

        if(isset($attributes['status']))
            unset($attributes['status']);

        $result->update ( $attributes );
        return $this->sendResponse ( $result);
    }



    public function delete($id) {


        $result= PushNotification::where('push_time', '>', DB::raw('now()'))
            ->where ( 'id', $id)
            ->first();
// 		find ( $id );

        if ($result) {
            $result->is_disabled = 1;
            $result->save();
            return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Notification Disabled' );
        } else {
            return $this->sendResponse ( false, self::NOT_ACCEPTABLE, 'Invalid or expired notification' );
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendTestNotification(Request $request)
    {
        $attributes = $request->getRawPost(true);

        $push = PushNotification::find(array_get($attributes, 'notification_id'));
        $user = User::where('phone', array_get($attributes, 'phone'))
            ->first();

        if (!$user || ! $push) {
            return $this->sendResponse( false, self::NOT_ACCEPTABLE, 'Invalid');
        }

        $data = json_decode($push->push, true);
        $data['where'] = [
            'userId' => (string) $user->id
        ];

        if ($push->title) {
            $iosPush = $data;
            $androidPush = $data;
            $title = $push->title;

            /**
             * send ios notification
             */
            $iosPush['data']['alert'] = [
                'title' => $title,
                'body' => array_get($iosPush, 'data.alert')
            ];
            $iosPush = $this->getWhereForDevice($iosPush, 'ios');
            ParsePush::send($iosPush);


            $androidPush['data'] = array_merge($androidPush['data'], [
                'title' => $title,
            ]);

            $androidPush = $this->getWhereForDevice($androidPush, 'android');
            ParsePush::send($androidPush);

        } else {
            $response = ParsePush::send($data);

        }

        return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Notification send.' );
    }

    /**
     * @param $data
     * @param $device
     * @return mixed
     */
    protected function getWhereForDevice($data, $device)
    {
        if (array_get($data, 'where')) {
            $data['where']['deviceType'] = [
                '$in' => [$device]
            ];
        } else {
            $data['where'] = [
                'deviceType'  => [
                    '$in' => [$device]
                ]
            ];
        }

        return $data;
    }
}
?>