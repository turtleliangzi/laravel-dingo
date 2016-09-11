<?php

namespace App\Api\V1\Controllers\Project;

use App\Api\V1\Controllers\BaseController;
use App\Department;
use App\Company;
use App\User;
use App\ProjectMember;
use App\Project;
use App\ProjectGoal;
use App\ProjectTask;
use DB;
use App\Dingding;
use App\Sendmail;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\ProjectTransformer;
use App\Api\V1\Transformers\ProjectMemberTransformer;
use App\Api\V1\Transformers\ProjectGoalTransformer;
use App\Api\V1\Transformers\ProjectTaskTransformer;


class ProjectController extends BaseController {

    /**
     * @SWG\Post(
     * path="/project/add",
     * summary="新增项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_name", in="query", required=true, description="项目名", type="string"),
     * @SWG\Parameter(name="project_type", in="query", required=true, description="项目类型", type="string"),
     * @SWG\Parameter(name="project_range", in="query", required=true, description="项目等级", type="string"),
     * @SWG\Parameter(name="project_desc", in="query", required=true, description="项目描述", type="string"),
     * @SWG\Parameter(name="etimated_time", in="query", required=true, description="项目预估完成时间", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="新增项目成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="新增项目失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function add(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $project = array(
            'project_name' => htmlspecialchars($request->get('project_name')),
            'project_type' => htmlspecialchars($request->get('project_type')),
            'project_range' => htmlspecialchars($request->get('project_range')),
            'project_desc' => htmlspecialchars($request->get('project_desc')),
            'etimated_time' => htmlspecialchars($request->get('etimated_time')),
            'create_time' => date('Y-m-d H:m:s'),
        );
        $project['company'] = $user['company'];
        $project['project_applicant'] = $user['name'];
        if (empty($project['project_name'])) {
            return $this->errorResponse("项目名不能为空", 406);
        }
        if (empty($project['project_type'])) {
            return $this->errorResponse("项目类型不能为空", 406);
        }
        if (empty($project['project_range'])) {
            return $this->errorResponse("项目等级不能为空", 406);
        }
        if (empty($project['etimated_time'])) {
            return $this->errorResponse("项目预估时间不能为空", 406);
        }

        $result = Project::create($project);
        if (!empty($result)) {
            return $this->successResponse("新增项目成功", 200);
        } else {
            return $this->errorResponse("新增项目失败", 500);
        }

    }

    /**
     * @SWG\Get(
     * path="/project/get/{kind}",
     * summary="获取未审核/已审核但未分配/已审核且分配/审核不通过的项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="kind", in="path", required=true, description="审核状态，0代表未审核，1代表已审核但未分配，2代表已审核且分配，3代表审核不通过", type="integer"),
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
     *   description="用户没找到"
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

    public function unaudited($kind) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $kind = (integer)($kind);
        $company = $user['company'];
        $unauditedProject = Project::unauditedProject($company, $kind);
        return $this->response->collection($unauditedProject, new ProjectTransformer);

    }


    /**
     * @SWG\Post(
     * path="/project/audit",
     * summary="审核项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="status", in="query", required=true, description="1审核通过，3审核不通过", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="审核项目成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="审核项目失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function audit(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $auditor = $user['name'];
        $project_id = (integer)($request->get('project_id'));
        $status = (integer)($request->get('status'));
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问", 406);
        }
        if (empty($status)) {
            return $this->errorResponse("请确认是审核通过还是不通过", 406);
        }
        $rs = Project::updateAuditStatus($project_id, array("approve_status"=>$status, "auditor" => $auditor, "audit_time" => date("Y-m-d H:m:s")));
        if ($rs === false) {
            if ($status === 1) {
                return $this->errorResponse("审核通过失败", 500);
            } else {
                return $this->errorResponse("审核不通过失败", 500);
            }
        } else {
            if ($status === 1) {
                return $this->successResponse("审核通过成功", 200);
            } else {
                return $this->successResponse("审核不通过成功", 200);
            }
        }

    }


    /**
     * @SWG\Post(
     * path="/project/distribute",
     * summary="项目分配项目经理和产品经理",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="project_manager", in="query", required=true, description="项目经理email", type="string"),
     * @SWG\Parameter(name="product_manager", in="query", required=true, description="产品经理email", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="分配成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="分配失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function distribute(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_manager_email = htmlspecialchars($request->get('project_manager'));
        $product_manager_email = htmlspecialchars($request->get('product_manager'));
        $project_id = (integer)($request->get('project_id'));
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        if (empty($project_manager_email)) {
            return $this->errorResponse("项目经理email不能为空", 406);
        }
        if (empty($product_manager_email)) {
            return $this->errorResponse("产品经理email不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问", 406);
        }
        if ($project['approve_status'] != 1) {
            return $this->errorResponse("该项目不能分配", 406);
        }
        $projectManager = User::findUserEmail($project_manager_email);
        if (empty($projectManager)) {
            return $this->errorResponse("该项目经理不存在", 406);
        }
        if ($projectManager['company'] != $company) {
            return $this->errorResponse("公司内不存在该项目经理", 406);
        }
        $productManager = User::findUserEmail($product_manager_email);
        if (empty($productManager)) {
            return $this->errorResponse("该产品经理不存在", 406);
        }
        if ($productManager['company'] != $company) {
            return $this->errorResponse("公司内不存在该产品经理", 406);
        }
        $product_manager = $productManager['id'];
        $project_manager = $projectManager['id'];
        $rs = Project::updateAuditStatus($project_id, array("approve_status"=>2, "project_manager" => $project_manager, "product_manager" => $product_manager, "distribute_time" => date("Y-m-d H:m:s")));
        if ($rs === false) {
            return $this->errorResponse("分配失败", 500);
        } else {
            ProjectMember::addMember(array("member"=>$project_manager, "project"=>$project_id, "project_role"=>"项目经理"));
            ProjectMember::addMember(array("member"=>$product_manager, "project"=>$project_id, "project_role"=>"产品经理"));
            Dingding::sendTextMessage($projectManager->dingding_id,$projectManager->name."你好！"."你已被分配为".$project['project_name']."的项目经理");
            return $this->successResponse("分配成功", 200);
        }

    }
    /**
     * @SWG\Get(
     * path="/project/myproject",
     * summary="获取我的项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取我的项目成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取我的项目失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getMyProject() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $uid = $user['id'];
        $projects = Project::getMyProject($uid);
        return $this->response->collection($projects, new ProjectTransformer);
    }


    /**
     * @SWG\Post(
     * path="/project/add_goal",
     * summary="添加目标",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="string"),
     * @SWG\Parameter(name="goal_name", in="query", required=true, description="目标名", type="string"),
     * @SWG\Parameter(name="goal_weight", in="query", required=true, description="目标权重", type="string"),
     * @SWG\Parameter(name="start_time", in="query", required=true, description="开始时间", type="string"),
     * @SWG\Parameter(name="etimated_end_time", in="query", required=true, description="预计结束时间", type="string"),
     * @SWG\Parameter(name="goal_order", in="query", required=true, description="执行顺序", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="添加目标成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="添加目标失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function addGoal(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $goal = array(
            'goal_name' => htmlspecialchars($request->get('goal_name')),
            'goal_weight' => htmlspecialchars($request->get('goal_weight')),
            'project' => intval($request->get('project_id')),
            'start_time' => htmlspecialchars($request->get('start_time')),
            'etimated_end_time' => htmlspecialchars($request->get('etimated_end_time')),
            'goal_order' => htmlspecialchars($request->get('goal_order')),
            'create_time' => date("Y-m-d H:m:s"),
        );
        $company = $user['company'];
        if (empty($goal['goal_name'])) {
            return $this->errorResponse("目标名不能为空", 406);
        }
        if (empty($goal['goal_weight'])) {
            return $this->errorResponse("目标权重不能为空", 406);
        }
        if (empty($goal['project'])) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        if (empty($goal['start_time'])) {
            return $this->errorResponse("开始时间不能为空", 406);
        }
        if (empty($goal['etimated_end_time'])) {
            return $this->errorResponse("预计结束时间不能为空", 406);
        }
        if (empty($goal['goal_order'])) {
            return $this->errorResponse("目标执行顺序不能为空", 406);
        }
        $project = Project::getById($goal['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        $rs = ProjectGoal::addGoal($goal);
        if ($rs) {
            return $this->successResponse("添加目标成功", 200);
        } else {
            return $this->errorResponse("添加目标失败", 500);
        }

    }
    /**
     * @SWG\Post(
     * path="/project/add_task",
     * summary="添加任务",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="goal", in="query", required=true, description="阶段目标", type="string"),
     * @SWG\Parameter(name="task_name", in="query", required=true, description="任务名", type="string"),
     * @SWG\Parameter(name="task_weight", in="query", required=true, description="任务权重", type="string"),
     * @SWG\Parameter(name="etimated_time", in="query", required=true, description="任务估时", type="string"),
     * @SWG\Parameter(name="task_priority", in="query", required=true, description="任务优先级", type="string"),
     * @SWG\Parameter(name="task_difficulty", in="query", required=true, description="任务难度", type="string"),
     * @SWG\Parameter(name="task_type", in="query", required=true, description="任务类型", type="string"),
     * @SWG\Parameter(name="task_desc", in="query", required=true, description="任务描述", type="string"),
     * @SWG\Parameter(name="reminder", in="query", required=true, description="被提醒者邮箱", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="添加任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="添加任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function addTask(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $task = array(
            'task_name' => htmlspecialchars($request->get('task_name')),
            'task_weight' => htmlspecialchars($request->get('task_weight')),
            'goal' => intval($request->get('goal')),
            'etimated_time' => htmlspecialchars($request->get('etimated_time')),
            'task_priority' => htmlspecialchars($request->get('task_priority')),
            'task_difficulty' => htmlspecialchars($request->get('task_difficulty')),
            'task_type' => htmlspecialchars($request->get('task_type')),
            'task_desc' => htmlspecialchars($request->get('task_desc')),
            'create_time' => date("Y-m-d H:m:s"),
        );
        $company = $user['company'];
        $reminder = htmlspecialchars($request->get('reminder'));
        if (empty($task['task_name'])) {
            return $this->errorResponse("任务名不能为空", 406);
        }
        if (empty($task['task_weight'])) {
            return $this->errorResponse("任务权重不能为空", 406);
        }
        if (empty($task['goal'])) {
            return $this->errorResponse("阶段目标不能为空", 406);
        }
        if (empty($task['etimated_time'])) {
            return $this->errorResponse("预估用时不能为空", 406);
        }
        if (empty($task['task_priority'])) {
            return $this->errorResponse("任务优先级不能为空", 406);
        }
        if (empty($task['task_difficulty'])) {
            return $this->errorResponse("任务难度不能为空", 406);
        }
        if (empty($task['task_type'])) {
            return $this->errorResponse("任务类型不能为空", 406);
        }
        if (empty($reminder)) {
            return $this->errorResponse("被提醒者邮箱不能为空", 406);
        }
        $remindUser = User::findUserEmail($reminder);
        if (empty($remindUser)) {
            return $this->errorResponse("该成员不存在", 406);
        }
        $goal = ProjectGoal::getById($task['goal']);
        if (empty($goal)) {
            return $this->errorResponse("该阶段目标不存在", 406);
        }
        $project = Project::getById($goal['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        $userExist = ProjectMember::getByProject($remindUser->id, $project->project_id);
        if (empty($userExist)) {
            return $this->errorResponse("该成员不在该项目组中", 406);
        }
        $task['reminder'] = $remindUser->id;
        $rs = ProjectTask::addTask($task);
        if ($rs) {
            //提醒成员
            $content = $remindUser['name']."你好，项目经理".$user['name']."在".$project['project_name']."中发布了一个新任务——".$task['task_name']." 并提到了你,任务详细信息如下，请及时到项目管理系统中领取任务，详细任务说明请查看你的钉邮！";
            $data = array (
                "touser" => $remindUser['dingding_id'],
                "url" => "http://pms.turtletl.com/#/ui/project/mission/".$project['project_id']."/".$goal['pg_id']."/".$rs,
                "task_name" => $task['task_name'],
                "type" => $task['task_type'],
                "etimated_time" => $task['etimated_time'],
                "description" => $task['task_desc'],
                "task_difficulty" => $task['task_difficulty'],
                "task_priority" => $task['task_priority'],
                'manager_name' => $user['name'],
                'content' => $content,
            );
            Dingding::sendOaTaskMessage($data);
            $data['email'] = $reminder;
            $data['name'] = $remindUser['name'];
            $data['task_weight'] = $task['task_weight'];
            $data['project_name'] = $project['project_name'];
            $data['content'] = "新任务提醒";

            Sendmail::sendMail($data, "email.addTask");
            return $this->successResponse("添加任务成功", 200);


        } else {
            return $this->errorResponse("添加任务失败", 500);
        }

    }
    /**
     * @SWG\Get(
     * path="/project/get/one/{project_id}",
     * summary="获取单个项目信息",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="添加任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="添加任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getOne($project_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $project_id = intval($project_id);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $company = $user['company'];
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        if ($project['approve_status'] < 2) {
            return $this->errorResponse("该项目没有审核通过或审核通过但未分配经理", 406);
        }
        return $this->response->item($project, new ProjectTransformer);
    }
    /**
     * @SWG\Get(
     * path="/project/get/goal/all/{project_id}",
     * summary="获取某个项目下的所有目标",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="添加任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="添加任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getGoalAll($project_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $project_id = intval($project_id);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $company = $user['company'];
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        if ($project['approve_status'] < 2) {
            return $this->errorResponse("该项目没有审核通过或审核通过但未分配经理", 406);
        }
        $projectGoals = ProjectGoal::getGoalAll($project_id);
        return $this->response->collection($projectGoals, new ProjectGoalTransformer);
    }
    /**
     * @SWG\Post(
     * path="/project/add_member",
     * summary="新增项目成员",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="email", in="query", required=true, description="成员邮箱", type="string"),
     * @SWG\Parameter(name="project_role", in="query", required=true, description="成员角色", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="添加项目成员成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="添加项目成员失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function addMember(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $member = array(
            'project' => intval($request->get('project_id')),
            'Project_role' => htmlspecialchars($request->get('project_role')),
            'create_time' => date("Y-m-d"),
        );
        $email = htmlspecialchars($request->get('email'));
        if (empty($member['project'])) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($member['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        $companyUser = User::findUserEmail($email);
        if (empty($companyUser)) {
            return $this->errorResponse("该成员不存在", 406);
        }
        if ($companyUser['company'] != $company) {
            return $this->errorResponse("该成员不在公司内", 406);
        }
        $memberExist = ProjectMember::getByProject($companyUser['id'], $member['project']);
        if (!empty($memberExist)) {
            return $this->errorResponse("该成员已在项目中", 406);
        }
        $member['member'] = $companyUser['id'];
        $rs = ProjectMember::addMember($member);
        if ($rs === false) {
            return $this->errorResponse("新增项目成员失败", 500);
        } else {
            // 发送钉钉消息及邮件
            $content = $companyUser['name']."你好，项目经理".$user['name']."邀你加入项目——".$project['project_name']." 并在项目中担任".$member['Project_role']."角色,项目详细情况请查看你的钉邮，请做好开发准备！";
            $data = array (
                "touser" => $companyUser['dingding_id'],
                "url" => "",
                "project_name" => $project['project_name'],
                "type" => $project['project_type'],
                "range" => $project['project_range'],
                "description" => $project['project_desc'],
                "etimated_time" => $project['etimated_time'],
                "manager_name" => $user['name'],
                'project_role' => $member['Project_role'],
                'content' => $content,
            );
            Dingding::sendOaMessage($data);
            $data['email'] = $email;
            $data['name'] = $companyUser['name'];
            $data['content'] = $content;
            $data['user'] = $companyUser['name'];
            $data['content'] = "新项目邀请";

            Sendmail::sendMail($data, "email.addMember");
            return $this->successResponse("新增项目成员成功", 200);
        }
    }
    /**
     * @SWG\Get(
     * path="/project/get_member/{project_id}",
     * summary="获取项目成员",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取项目成员成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取项目成员失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getMember($project_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("非法访问，该项目不在公司内", 406);
        }
        $members = ProjectMember::getMember($project_id);
        return $this->response->collection($members, new ProjectMemberTransformer);
    }

    /**
     * @SWG\Get(
     * path="/project/get_task/{project_id}/{goal_id}/{status}",
     * summary="获取阶段目标下某个状态下的所有任务",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="goal_id", in="path", required=true, description="阶段目标id", type="integer"),
     * @SWG\Parameter(name="status", in="path", required=true, description="任务状态，0待处理、1进行中、2已完成", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取阶段目标任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取阶段目标任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getTask($project_id, $goal_id, $status) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);
        $goal_id = intval($goal_id);
        $status = intval($status);
        if (empty($project_id)) {
            return $this->errorResponse("项目不能为空", 406);
        }
        if (empty($goal_id)) {
            return $this->errorResponse("阶段目标不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不能为空", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if (empty($goal)) {
            return $this->errorResponse("该阶段目标不存在", 406);
        }
        if ($goal['project'] != $project_id) {
            return $this->errorResponse("非法访问，该阶段目标不在该项目内", 406);
        }
        $tasks = ProjectTask::getTask($goal_id, $status);
        return $this->response->collection($tasks, new ProjectTaskTransformer);
    }
    /**
     * @SWG\Get(
     * path="/project/get/task/one/{goal_id}/{task_id}",
     * summary="获取单个任务信息",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="goal_id", in="path", required=true, description="阶段目标id", type="integer"),
     * @SWG\Parameter(name="task_id", in="path", required=true, description="任务id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取任务详情成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取任务详情失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getTaskOne($goal_id, $task_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $goal_id = intval($goal_id);
        $task_id = intval($task_id);
        if (empty($goal_id)) {
            return $this->errorResponse("阶段目标不能为空", 406);
        }
        if (empty($task_id)) {
            return $this->errorResponse("任务id不能为空", 406);
        }
        $task = ProjectTask::getById($task_id);
        if (empty($task)) {
            return $this->errorResponse("该任务不存在", 406);
        }
        if ($task['goal'] != $goal_id) {
            return $this->errorResponse("该任务不在该阶段目标中", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if (empty($goal)) {
            return $this->errorResponse("该阶段目标不存在", 406);
        }
        $project = Project::getById($goal['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        return $this->response->item($task, new ProjectTaskTransformer);
    }

    /**
     * @SWG\Post(
     * path="/project/complete/task",
     * summary="完成任务",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="goal_id", in="query", required=true, description="阶段目标id", type="integer"),
     * @SWG\Parameter(name="task_id", in="query", required=true, description="任务id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="完成任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="完成任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function completeTask(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $goal_id = intval($request->get('goal_id'));
        $task_id = intval($request->get('task_id'));
        if (empty($goal_id)) {
            return $this->errorResponse("阶段目标不能为空", 406);
        }
        if (empty($task_id)) {
            return $this->errorResponse("任务id不能为空", 406);
        }
        $task = ProjectTask::getById($task_id);
        if (empty($task)) {
            return $this->errorResponse("该任务不存在", 406);
        }
        if ($task['goal'] != $goal_id) {
            return $this->errorResponse("该任务不在该阶段目标中", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if (empty($goal)) {
            return $this->errorResponse("该阶段目标不存在", 406);
        }
        $project = Project::getById($goal['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        $data = array(
            'progress_status' => 2,
            'executor' => $user['id'],
            'update_time' => date('Y-m-d H:m:s'),
            'end_time' => date('Y-m-d H:m:s'),
        );
        $rs = ProjectTask::updateStatus($data, $task_id);
        if ($rs === false) {
            return $this->errorResponse("完成任务失败", 500);
        } else {
            $project_manager = User::getById($project['project_manager']);
            $project_role = ProjectMember::getByProject($user['id'],$project['project_id'])->project_role;
            Dingding::sendTextMessage($project_manager->dingding_id,"项目经理".$project_manager->name."你好！".$project_role.$user['name']."刚刚完成了".$project['project_name']."的".$task['task_name']);
            return $this->successResponse("完成任务成功", 200);
        }
    }
    /**
     * @SWG\Post(
     * path="/project/deal",
     * summary="开始/上线/结束项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="开始/结束项目成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="开始/结束项目失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function dealTask(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($request->get('project_id'));
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        if ($project['approve_status'] == 2) {
            $data = array(
                'approve_status' => 3,
                'updated_at' => date('Y-m-d H:m:s'),
                'start_time' => date('Y-m-d H:m:s'),
            );

        } elseif ($project['approve_status'] == 3) {
            $data = array(
                'approve_status' => 4,
                'updated_at' => date('Y-m-d H:m:s'),
                'online_time' => date('Y-m-d H:m:s'),
            );
        } elseif ($project['approve_status'] == 4) {
            $data = array(
                'approve_status' => 5,
                'updated_at' => date('Y-m-d H:m:s'),
                'end_time' => date('Y-m-d H:m:s'),
            );

        }
        $rs = Project::updateStatus($data, $project_id);
        if ($rs === false) {
            if ($project['approve_status'] == 2) {
                return $this->errorResponse("开始项目失败", 500);
            } elseif ($project['approve_status'] == 3) {
                return $this->errorResponse("上线项目失败", 500);
            } else {
                return $this->errorResponse("结束项目失败", 500);
            }
        } else {
            if ($project['approve_status'] == 2) {
                return $this->successResponse("开始项目成功", 200);
            } elseif ($project['approve_status'] == 3) {
                return $this->successResponse("上线项目成功", 200);
            } else {
                return $this->successResponse("结束项目成功", 200);
            }
        }
    }


    /**
     * @SWG\Post(
     * path="/project/receive/task",
     * summary="领取任务",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="goal_id", in="query", required=true, description="阶段目标id", type="integer"),
     * @SWG\Parameter(name="task_id", in="query", required=true, description="任务id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="领取任务成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="领取任务失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function receiveTask(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $goal_id = intval($request->get('goal_id'));
        $task_id = intval($request->get('task_id'));
        if (empty($goal_id)) {
            return $this->errorResponse("阶段目标不能为空", 406);
        }
        if (empty($task_id)) {
            return $this->errorResponse("任务id不能为空", 406);
        }
        $task = ProjectTask::getById($task_id);
        if (empty($task)) {
            return $this->errorResponse("该任务不存在", 406);
        }
        if ($task['goal'] != $goal_id) {
            return $this->errorResponse("该任务不在该阶段目标中", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if (empty($goal)) {
            return $this->errorResponse("该阶段目标不存在", 406);
        }
        $project = Project::getById($goal['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        $data = array(
            'progress_status' => 1,
            'start_time' => date('Y-m-d H:m:s'),
            'executor' => $user['id'],
            'update_time' => date('Y-m-d H:m:s'),
        );
        $rs = ProjectTask::updateStatus($data, $task_id);
        if ($rs === false) {
            return $this->errorResponse("领取任务失败", 500);
        } else {
            //根据项目id获取项目经理钉邮，然后发消息通知已经领取
            $project_manager = User::getById($project['project_manager']);
            $project_role = ProjectMember::getByProject($user['id'],$project['project_id'])->project_role;
            Dingding::sendTextMessage($project_manager->dingding_id,"项目经理".$project_manager->name."你好！".$project_role.$user['name']."刚刚领取了".$project['project_name']."的".$task['task_name']);
            return $this->successResponse("领取任务成功", 200);
        }
    }

    /**
     * @SWG\Get(
     * path="/project/get_goal/one/{project_id}/{goal_id}",
     * summary="获取单个目标信息",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="goal_id", in="path", required=true, description="目标id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取单个目标细信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取单个目标信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getOneGoal($project_id, $goal_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);
        $goal_id = intval($goal_id);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        if (empty($goal_id)) {
            return $this->errorResponse("目标id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if ($goal['project'] != $project_id) {
            return $this->errorResponse("该目标不再该项目内", 406);
        }
        return $this->response->item($goal, new ProjectGoalTransformer);
    }
    /**
     * @SWG\Post(
     * path="/project/deal/goal",
     * summary="开始/测试/结束目标",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="goal_id", in="query", required=true, description="目标id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="开始/结束目标成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="开始/结束目标失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function dealGoal(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($request->get('project_id'));
        $goal_id = intval($request->get('goal_id'));
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        if (empty($goal_id)) {
            return $this->errorResponse("目标id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return  $this->errorResponse("该项目不在公司内", 406);
        }
        $goal = ProjectGoal::getById($goal_id);
        if ($goal['project'] != $project_id) {
            return $this->errorResponse("该目标不再该项目内", 406);
        }
        if ($goal['progress_status'] == 0) {
            $data = array(
                'progress_status' => 1,
                'start_time' => date("Y-m-d"),
                'update_time' => date("Y-m-d H:m:s")
            );
        } elseif ($goal['progress_status'] == 1) {
            $data = array(
                'progress_status' => 2,
                'update_time' => date("Y-m-d H:m:s"),
            );
        } elseif ($goal['progress_status'] == 2) {
            $data = array(
                'progress_status' => 3,
                'update_time' => date("Y-m-d H:m:s"),
                'actual_end_time' => date("Y-m-d"),
            );
        }
        $rs = ProjectGoal::updateStatus($data, $goal_id);
        if ($rs === false) {
            if ($goal['progress_status'] == 0) {
                return $this->errorResponse("开始目标失败", 500);
            } elseif ($goal['progress_status'] == 1) {
                return $this->errorResponse("测试目标失败", 500);
            } else {
                return $this->errorResponse("结束目标失败", 500);
            }
        } else {
            if ($goal['progress_status'] == 0) {
                return $this->successResponse("开始目标成功", 200);
            } elseif ($goal['progress_status'] == 1) {
                return $this->successResponse("测试目标成功", 200);
            } else {
                return $this->successResponse("结束目标成功", 200);
            }
        }

    }
    /**
     * @SWG\Get(
     * path="/project/all",
     * summary="获取公司下所有审核通过并分配了的项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
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
     *   description="用户没找到"
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

    public function getAllProject() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];

        $projects = Project::getAllProject($company);
        return $this->response->collection($projects, new ProjectTransformer());

    }

    /**
     * @SWG\Get(
     * path="/project/checkStatus/{status}",
     * summary="获取公司下相应项目状态数目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="status", in="path", required=true, description="项目状态值", type="integer"),
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
     *   description="用户没找到"
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
    public  function checkStatus($status){
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];

        $statuss = Project::checkStatus($company,$status);
        return response()->json($statuss);
    }
    /**
     * @SWG\Get(
     * path="/project/project_manager/{project_id}",
     * summary="判断当前用户是否为该项目的项目经理",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="判断成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="判断失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function isProjectManager($project_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);

        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 404);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不在该公司内", 404);
        }
        if ($user['id'] == $project['project_manager']) {
            $data = array(
                'data' => true,
            );
        } else {
            $data = array(
                'data' => false,
            );
        }
        return response()->json($data);
    }

    /**
     * @SWG\Get(
     * path="/project/getAllTaskInfo/{task_status}",
     * summary="获得用户公司的所有项目的某个状态任务的信息",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="task_status", in="path", required=true, description="任务状态", type="integer"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */
    public function getAllTaskInfo($task_status){
      if (! $user = JWTAuth::parseToken()->authenticate()) {
          return $this->errorResponse("用户没找到", 404);
      }
      $company = $user['company'];
      $task_status = intval($task_status);
      $tasks = ProjectTask::getSomeTask($company, $task_status);
      return $this->response->collection($tasks, new ProjectTaskTransformer());

    }
    /**
     * @SWG\Get(
     * path="/project/task/doing",
     * summary="获得某个项目成员正在进行的任务",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
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
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */
    public function getDoingTask(){
      if (! $user = JWTAuth::parseToken()->authenticate()) {
          return $this->errorResponse("用户没找到", 404);
      }
      $uid = $user['id'];
      $tasks = ProjectTask::getDoingTask($uid);
      return $this->response->collection($tasks, new ProjectTaskTransformer());

    }
}
