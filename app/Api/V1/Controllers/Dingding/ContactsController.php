<?php

namespace App\Api\V1\Controllers\Dingding;

use App\Api\V1\Controllers\BaseController;
use App\Dingding;


class ContactsController extends BaseController {

    /*
     * 主动调用获取access_token
     * @author turtle
     * create_time 2016-08-03
     */

    public function getAccessToken() {
        $rs = Dingding::getAccessToken();
        var_dump($rs);
    }
    /*
     * 主动调用获取部门列表
     * @author turtle
     * create_time 2016-08-03
     */

    public function getDepartment() {
        $rs = Dingding::getDepartment();
        var_dump($rs);
    }
    /*
     * 主动调用获取部门详情
     * @author turtle
     * create_time 2016-08-03
     */

    public function getDepartmentInfo() {
        $rs = Dingding::getDepartmentInfo();
        var_dump($rs);
    }
    /*
     * 主动调用获取部门成员
     * @author turtle
     * create_time 2016-08-03
     */

    public function getDepartmentMember() {
        $rs = Dingding::getDepartmentMember();
        var_dump($rs);
    }
    /*
     * 主动调用获取部门成员详情
     * @author turtle
     * create_time 2016-08-03
     */

    public function getMemberInfo() {
        $rs = Dingding::getMemberInfo();
        var_dump($rs);
    }

    /*
     * 推送消息
     * @author turtle
     * create_time 2016-08-04
     */

    public function sendMessage() {
        $rs = Dingding::sendTextMessage('01023301643881', '定时发送消息测试');
    }
}
