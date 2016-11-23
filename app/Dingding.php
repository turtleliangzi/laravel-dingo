<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dingding {

    /*
     * 主动调佣获取access_token
     * @author turtle
     * create_time 2016-08-03
     */

    public static function getAccessToken() {
        $corpid = "a";
        $corpSecret = "J-OPSvdXc01FC8Kqjcj2m5H6";

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/gettoken?corpid=".$corpid."&corpsecret=".$corpSecret;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = json_decode($output);
        $rs = (array)($rs);
        return $rs['access_token'];

    }
    /*
     * 主动调佣获取部门列表
     * @author turtle
     * create_time 2016-08-03
     */

    public static function getDepartment() {
        $access_token = self::getAccessToken();

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/department/list?access_token=".$access_token;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = (array)(json_decode($output));
        return $rs;
    }

    /*
     * 主动调佣获取部门详情
     * @author turtle
     * create_time 2016-08-03
     */

    public static function getDepartmentInfo($id) {
        $access_token = self::getAccessToken();

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/department/get?access_token=".$access_token."&id=".$id;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = (array)(json_decode($output));
        return $rs;

    }
    /*
     * 主动调佣获取部门成员
     * @author turtle
     * create_time 2016-08-03
     */

    public static function getDepartmentMember() {
        $access_token = self::getAccessToken();

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/user/simplelist?access_token=".$access_token."&department_id=1793397";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = (array)(json_decode($output));
        return $rs;

    }
    /*
     * 主动调佣获取部门成员详情
     * @author turtle
     * create_time 2016-08-03
     */

    public static function getMemberInfo($depDing_id) {
        $access_token = self::getAccessToken();

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/user/list?access_token=".$access_token."&department_id=".$depDing_id;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = (array)(json_decode($output));
        return $rs;

    }
    /*
     * 发送企业文本消息
     * @author turtle
     * create_time 2016-08-05
     */
    public static function sendTextMessage($touser, $content="你有新的消息，请注意查收！") {
        $data = array(
            "touser" => $touser,
            "toparty" => "",
            "agentid" => "3762686",
            "msgtype" => "text",
            'text' => array(
                "content" => $content
            )
        );

        return self::sendMessage($data);

    }

    /*
     * 发送企业OA消息-新增项目成员
     * @author turtle
     * create_time 2016-08-05
     */

    public static function sendOaMessage($project) {
        $data = array (
            "touser" => $project['touser'],
            "toparty" => "",
            "agentid" => "3762686",
            'msgtype' => 'oa',
            'oa' => array (
                'message_url' => $project['url'],
                'head' => array (
                    'bgcolor' => 'FF0089CD',
                    'text' => '江西安纳斯信息科技有限公司',
                ),
                'body' => array (
                    'title' => '新项目邀请',
                    'form' => array (
                        0 => array (
                            'key' => '项目名:',
                            'value' => $project['project_name'],
                        ),
                        1 => array (
                            'key' => '项目类型:',
                            'value' => $project['type'],
                        ),
                        2 => array (
                            'key' => '项目等级:',
                            'value' => $project['range'],
                        ),
                        3 => array (
                            'key' => '项目描述:',
                            'value' => $project['description'],
                        ),
                        4 => array (
                            'key' => '预计用时:',
                            'value' => $project['etimated_time'],
                        ),
                        5 => array (
                            'key' => '担任角色:',
                            'value' => $project['project_role'],
                        ),
                    ),
                    'content' => $project['content'],
                    'author' => $project['manager_name'],
                ),
            ),
        );
        return self::sendMessage($data);
    }

    /*
     * 发送企业OA消息-添加任务提醒成员
     * @author turtle
     * create_time 2016-08-09
     */

    public static function sendOaTaskMessage($project) {
        $data = array (
            "touser" => $project['touser'],
            "toparty" => "",
            "agentid" => "3762686",
            'msgtype' => 'oa',
            'oa' => array (
                'message_url' => $project['url'],
                'head' => array (
                    'bgcolor' => 'FF0089CD',
                    'text' => '江西安纳斯信息科技有限公司',
                ),
                'body' => array (
                    'title' => '新任务提醒',
                    'form' => array (
                        0 => array (
                            'key' => '任务名:',
                            'value' => $project['task_name'],
                        ),
                        1 => array (
                            'key' => '任务类型:',
                            'value' => $project['type'],
                        ),
                        2 => array (
                            'key' => '任务估时:',
                            'value' => $project['etimated_time'].'天',
                        ),
                        3 => array (
                            'key' => '任务描述:',
                            'value' => $project['description'],
                        ),
                        4 => array (
                            'key' => '任务难度:',
                            'value' => $project['task_difficulty'],
                        ),
                        5 => array (
                            'key' => '任务优先级:',
                            'value' => $project['task_priority'],
                        ),
                    ),
                    'content' => $project['content'],
                    'author' => $project['manager_name'],
                ),
            ),
        );
        return self::sendMessage($data);
    }

    /*
     * 下班前任务消息提醒
     * @author turtle
     * create_time 2016-08-18
     */

    public static function sendOaDoingTaskMessage($project) {
        $data = array (
            "touser" => $project['touser'],
            "toparty" => "",
            "agentid" => "3762686",
            'msgtype' => 'oa',
            'oa' => array (
                'message_url' => $project['url'],
                'head' => array (
                    'bgcolor' => 'FF0089CD',
                    'text' => '江西安纳斯信息科技有限公司',
                ),
                'body' => array (
                    'title' => '任务提醒',
                    'content' => $project['content'],
                    'author' => '廖亮',
                ),
            ),
        );
        return self::sendMessage($data);
    
    }
    /*
     * 发送企业信息
     * @author turtle
     * create_time 2016-08-03
     */

    public static function sendMessage($data) {
        $access_token = self::getAccessToken();

        $ch = curl_init();
        $url = "https://oapi.dingtalk.com/message/send?access_token=".$access_token;
        $data = json_encode($data);
        $header = array(
            'Content-Type: application/json',
        );
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $output = curl_exec($ch);
        curl_close ( $ch );
        $rs = (array)(json_decode($output));
        return $rs;

    }

}
