<?php

namespace App\Api\V1\Controllers\SendMail;

use App\Api\V1\Controllers\BaseController;
use App\Sendmail;
use App\Dingding;


class SendMailController extends BaseController {

    /**
     * 发送邮件测试
     * @author turtle
     * create_time 2016-08-03 
     */

    public function sendMail() {
        Sendmail::sendMail();
        Dingding::sendMessage();
    }
}
