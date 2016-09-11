<?php

namespace App;

use DB;
use Mail;

class Sendmail {
    /*
     * 发送邮件
     * @author turtle
     * createtime 2016-08-03
     */
    public static function sendMail($data, $view) {
        Mail::send($view, $data, function($message) use($data) {
            $message->from('info@anasit.com', '江西安纳斯信息科技有限公司');
            $message->to($data['email'], $data['name'])->subject($data['content']);
        });
    }
}
