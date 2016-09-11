<?php

namespace App\Api\V1\Controllers\Demand;

use App\Api\V1\Controllers\BaseController;
use App\Demand;
use App\Project;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\DemandTransformer;


class DemandController extends BaseController {

    /** 
     * @SWG\Post(
     * path="/demand/add",
     * summary="添加需求",
     * tags={"Demands"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="demand_title", in="query", required=true, description="需求标题", type="string"),
     * @SWG\Parameter(name="demand_desc", in="query", required=true, description="需求描述", type="string"),
     * @SWG\Parameter(name="project", in="query", required=true, description="项目id", type="integer"),
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
     *   description="新增需求成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="新增需求失败"
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
        $demand = array(
            'demand_title' => htmlspecialchars($request->get('demand_title')),
            'demand_desc' => htmlspecialchars($request->get('demand_desc')),
            'project' => intval($request->get('project')),
            'committer' => intval($user['id']),
        );
        $company = $user['company'];
        if (empty($demand['demand_title'])) {
            return $this->errorResponse("需求标题不能为空", 406);
        }
        if (empty($demand['demand_desc'])) {
            return $this->errorResponse("需求描述不能为空", 406);
        }

        $project = Project::getById($demand['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再公司内", 406);
        }

        $result = Demand::create($demand);
        if (!empty($result)) {
            return $this->successResponse("新增需求成功", 200);
        } else {
            return $this->errorResponse("新增需求失败", 500);
        }

    }
    /** 
     * @SWG\Get(
     * path="/demand/all/{project}/{kind}",
     * summary="已批准/待批准/未批准需求",
     * tags={"Demands"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="kind", in="path", required=true, description="需求状态,0代表待批准、1代表已批准、-1代表未批准", type="integer"),
     * @SWG\Parameter(name="project", in="path", required=true, description="项目id", type="integer"),
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
     *   description="获取需求成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取需求失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function demandAll($project, $kind) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project);
        $kind = intval($kind);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }

        $project = Project::getById($project);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再公司内", 406);
        }

        $demands = Demand::getAll($project_id, $kind);
        return $this->response->collection($demands, new DemandTransformer());

    }
}
