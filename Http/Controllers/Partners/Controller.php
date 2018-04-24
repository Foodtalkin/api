<?php

namespace App\Http\Controllers\Partners;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use DB;

class Controller extends BaseController
{
    const PAGE_SIZE = 100;
    const MAX_PAGE_SIZE = 150;

    const SUCCESS_OK = 200; // 200 => 'OK',
    const REQUEST_ACCEPTED = 202;	// 202 => 'Accepted',
    const SUCCESS_OK_NO_CONTENT = 204; // 200 => 'OK',
    const UN_AUTHORIZED = 401;	// 401 => 'Unauthorized',
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403; // 403 => 'Forbidden',
    const NO_ENTITY = 404; // 404 => 'Not Found',
    const NOT_ACCEPTABLE = 406; // 404 => 'Not Found',

    /**
     * @var int
     */
    protected $pageSize;

    public function __construct()
    {
		if (isset($_GET['page_size']) && $_GET['page_size'] < self::MAX_PAGE_SIZE ) {
			$this->pageSize = $_GET['page_size']; 
		} else {
			$this->pageSize = self::PAGE_SIZE;
		}
	}

    /**
     * @param $option
     * @return array
     */
	private static final function statusMessage($option) {
		$status = [];
		$ok = 'OK';
		$error = 'ERROR';
		
		switch ($option) {
			case self::SUCCESS_OK :
				$status ['message'] = 'Success';
				$status ['status'] = $ok;
				$status ['code'] = '200';
				break;
				
			case self::REQUEST_ACCEPTED :
				$status ['message'] = 'Request Accepted';
				$status ['status'] = $ok;
				$status ['code'] = '202';
				break;
			
			case self::UN_AUTHORIZED :
				$status ['message'] = 'Login is Required!';
				$status ['status'] = $error;
				$status ['code'] = '401';
				break;
				
			case self::FORBIDDEN :
				$status ['message'] = 'Forbidden Access!';
				$status ['status'] = $error;
				$status ['code'] = '403';
				break;
				
			case self::NO_ENTITY :
				$status ['message'] = 'No Such Entity!';
				$status ['status'] = $error;
				$status ['code'] = '404';
				break;

			case self::NOT_ACCEPTABLE :
				$status ['message'] = 'Unacceptable values';
				$status ['status'] = $error;
				$status ['code'] = '406';
				break;

			case self::PAYMENT_REQUIRED:
				$status ['message'] = 'Payment Required';
				$status ['status'] = $error;
				$status ['code'] = '402';
				break;

			default :
				$status ['message'] = 'Success';
				$status ['status'] = $ok;
				$status ['code'] = '200';
				break;
		}

		return $status;
	}

    /**
     * @param $responseData
     * @param int $status
     * @param null $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public static final function sendResponse($responseData, $status = self::SUCCESS_OK, $message = null)
    {
		$responseData = response()->json($responseData)
            ->getData(true);
		
		if(is_array($responseData)) {
            array_walk_recursive($responseData,'toJson');
        }

		if (! empty($responseData) && $status == self::SUCCESS_OK) {
			
			$response = self::statusMessage($status);
			if (! is_null($message)) {
				$response ['message'] = $message;
			}
			$response ['result'] = $responseData;
		} else {
			if ($status != self::SUCCESS_OK) {
				$response = self::statusMessage($status);
			} else {
				$response = self::statusMessage(self::NO_ENTITY);
			}
			
			if (! is_null($message)) {
				$response ['message'] = $message;
			}
			$response ['result'] = $responseData;
		}
		
		$response['date_time'] = date('Y-m-d H:i:s');
		$response['api'] = $_SERVER['REQUEST_URI'];
		
		return response()->json($response);
	}
}




