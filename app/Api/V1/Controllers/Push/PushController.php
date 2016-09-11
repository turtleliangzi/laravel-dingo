<?php

namespace App\Api\V1\Controllers\Push;

use App\Api\V1\Controllers\BaseController;
use App\ProjectTask;
use App\User;
use App\ProjectMember;
use App\Dingding;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\CompanyTransformer;
use App\Api\V1\Transformers\CompanyMemberTransformer;
use Illuminate\Support\Arr;


class PushController extends BaseController {

    /** 
     * @SWG\Get(
     * path="/push/task/undone",
     * summary="获取项目成员还未完成的任务",
     * tags={"Pushs"},
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getUndoneTask(Request $request) {
        $company = 'anasit-47968';
        $members = ProjectMember::getProjectMember($company);
        /* $members = json_decode($members); */
        /* $members = (array)($members); */
        foreach($members as $k=>$member) {
            $members[$k] = (array)($member);
            $tasks = ProjectTask::getDoingTask($members[$k]['member']);
            $count = count($tasks);
            if ($count === 0) {
                $message =  array (
                    'content' => "不错哟，今天效率值满满的，所有任务都完成了，再接再励！",
                );
            } elseif ($count < 3) {
                $message = array (
                    'content' => '到目前为止，你还有'.$count.'个任务未完成，但总体来说还是完成的不错，继续努力哦，点击查看任务详情！',
                );
            } else {
                $message = array (
                    'content' => '到目前为止，你还有'.$count.'个任务未完成，情况似乎有点糟糕，但不要放弃，再努力一点，你就能超越自己，加油！点击查看任务详情。',

                );

            }
            $message['touser'] = $members[$k]['dingding_id'];
            $message['url'] = 'http://pms.turtletl.com/#/ui/project/complete';
            $rs = Dingding::sendOaDoingTaskMessage($message);
        }
        return $rs;
    }
    /** 
     * @SWG\Get(
     * path="/push/task/remind",
     * summary="上班前任务提醒",
     * tags={"Pushs"},
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="发送成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="发送失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function taskRemind(Request $request) {
        $company = 'anasit-47968';
        $members = ProjectMember::getProjectMember($company);
        foreach($members as $k=>$member) {
            $members[$k] = (array)($member);
            $doingTasks = ProjectTask::getDoingTask($members[$k]['member']);
            $doingCount = count($doingTasks);
            $undoTask = ProjectTask::getUndoTask($members[$k]['member']);
            if ($count === 0) {
                $message =  array (
                    'content' => "不错哟，今天效率值满满的，所有任务都完成了，再接再励！",
                );
            } elseif ($count < 3) {
                $message = array (
                    'content' => '到目前为止，你还有'.$count.'个任务未完成，但总体来说还是完成的不错，继续努力哦，点击查看任务详情！',
                );
            } else {
                $message = array (
                    'content' => '到目前为止，你还有'.$count.'个任务未完成，情况似乎有点糟糕，但不要放弃，再努力一点，你就能超越自己，加油！点击查看任务详情。',

                );

            }
            $message['touser'] = $members[$k]['dingding_id'];
            $message['url'] = 'http://pms.turtletl.com/#/ui/project/complete';
            $rs = Dingding::sendOaDoingTaskMessage($message);
        }
        return $rs;
    }
}
