<?php

namespace  App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
date_default_timezone_set('Asia/Shanghai');

class BaseController extends Controller {
    use Helpers;

    /*
     * error resposne
     * @param message
     * @param status
     */

    protected function errorResponse($message = "不能为空", $status = 406) {
        
        $response = array(
            'error' => $message,
            'status' => $status,
        );
        return response()->json($response);
    }

    /*
     * success response
     * @param message
     * @param status
     */

    protected function successResponse($message = "请求成功", $status = 200) {
        $response = array(
            'success' => $message,
            'status' => $status,
        );
        return response()->json($response);
    }
}
