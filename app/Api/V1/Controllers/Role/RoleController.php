<?php

namespace App\Api\V1\Controllers\Role;

use App\Api\V1\Controllers\BaseController;
use DB;
use App\RoleType;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\RoleTypeTransformer;


class RoleController extends BaseController {

    /** 
     * @SWG\Get(
     * path="/role/role_type/{grade}",
     * summary="获取角色类型",
     * tags={"Roles"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="grade", in="path", required=true, description="类型等级", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取角色类型成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取角色类型失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getRoleType($grade) {
        $grade = (integer)($grade);
        $roleTypes= RoleType::getRoleType($grade);
        return $this->response->collection($roleTypes, new RoleTypeTransformer);
    }
}
