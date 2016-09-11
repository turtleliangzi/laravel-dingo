<?php

namespace App\Api\V1\Controllers\UserGroup;

use App\Api\V1\Controllers\BaseController;
use App\UserGroup;
use App\User;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\UserGroupTransformer;
use App\Api\V1\Transformers\MemberGroupTransformer;


class UserGroupController extends BaseController {

    /** 
     * @SWG\Post(
     * path="/group/add",
     * summary="新建用户组",
     * tags={"Groups"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="group_name", in="query", required=true, description="用户组名", type="string"),
     * @SWG\Parameter(name="permissions", in="query", required=true, description="权限", type="string"),
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
     *   description="新增用户组成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="新增用户组失败"
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
        $group = array(
            'group_name' => htmlspecialchars($request->get('group_name')),
            'permission' => json_encode($request->get('permissions')),
            'company' => $user['company'],
            'create_time' => date("Y-m-d H:m:s"), 
            'update_time' => date("Y-m-d H:m:s"), 
        );
        if (empty($group['group_name'])) {
            return $this->errorResponse("用户组名不能为空", 406);
        }
        if (empty($group['permission'])) {
            return $this->errorResponse("权限不能为空", 406);
        }

        $groupExist = UserGroup::getGroupByName($group['group_name'], $user['company']);
        if (!empty($groupExist)) {
            return $this->errorResponse("该用户组名已存在", 406);
        }

        $result = UserGroup::create($group, $user['company']);
        if (!empty($result)) {
            return $this->successResponse("新增用户组成功", 200);
        } else {
            return $this->errorResponse("新增用户组失败", 500);
        }

    }

    /** 
     * @SWG\Get(
     * path="/group/all",
     * summary="获取公司下的所有用户组",
     * tags={"Groups"},
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
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取所有用户组成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取所有用户组失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getAllGroup() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $company = $user['company'];

        $groups = UserGroup::getAllGroup($company);
        foreach($groups as $k=>$group) {
            $groups[$k]['permission'] = json_decode($group['permission']);
        }
        return $this->response->collection($groups, new UserGroupTransformer());

    }
    /** 
     * @SWG\Get(
     * path="/group/member",
     * summary="获取公司下的所有用户及所在用户组",
     * tags={"Groups"},
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

    public function getMemberGroup() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $company = $user['company'];

        $groups = User::getMemberGroup($company);
        return $this->response->collection($groups, new MemberGroupTransformer());

    }

    /** 
     * @SWG\Get(
     * path="/user/group/info/{uid}",
     * summary="获取单个用户用户组信息",
     * tags={"Groups"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="integer"),
     * @SWG\Parameter(name="uid", in="path", required=true, description="用户id", type="string"),
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

    public function getUserGroup($uid) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $company = $user['company'];
        $id = intval($uid);

        $userExist = User::find($id);
        if (empty($userExist)) {
            return $this->errorResponse("该用户不存在", 404);
        }
        if ($userExist['company'] != $company) {
            return $this->errorResponse("该用户不再公司内", 404);
        }

        $userGroup = User::getUserGroup($id);
        return $this->response->item($userGroup, new MemberGroupTransformer());

    }

    /** 
     * @SWG\Post(
     * path="/group/change_group",
     * summary="变更用户组",
     * tags={"Groups"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="uid", in="query", required=true, description="用户id", type="integer"),
     * @SWG\Parameter(name="group_id", in="query", required=true, description="用户组id", type="integer"),
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
     *   description="变更用户组成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="变更用户组失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function changeUserGroup(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $company = $user['company'];
        $id = intval($request->get('uid'));
        $group_id = intval($request->get('group_id'));

        $userExist = User::find($id);
        if (empty($userExist)) {
            return $this->errorResponse("该用户不存在", 404);
        }
        if ($userExist['company'] != $company) {
            return $this->errorResponse("该用户不再公司内", 404);
        }

        $groupExist = UserGroup::getGroupById($group_id);
        if (empty($groupExist)) {
            return $this->errorResponse("该用户组不存在", 404);
        }
        if ($groupExist['company'] != $company) {
            return $this->errorResponse("该用户组不在公司内", 404);
        }

        $rs = User::changeUserGroup($id, $group_id);
        if ($rs === false) {
            return $this->errorResponse("变更用户组失败", 500);
        } else {
            return $this->successResponse("变更用户组成功", 200);
        }
    }
    /** 
     * @SWG\Get(
     * path="/group/one/{group_id}",
     * summary="获取单个用户组信息",
     * tags={"Groups"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="group_id", in="path", required=true, description="用户组id", type="integer"),
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
     *   description="获取单个用户组信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取单个用户组信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getOneGroup($group_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }

        $company = $user['company'];
        $group_id = intval($group_id);

        if (empty($group_id)) {
            return $this->errorResponse("该用户组id不存在", 404);
        }

        $group = UserGroup::getGroupById($group_id);
        if (empty($group)) {
            return $this->errorResponse("该用户组不存在", 404);
        }
        if ($group['company'] != $company) {
            return $this->errorResponse("该用户组不在公司内", 404);
        }
        $group['permission'] = json_decode($group['permission']);

        return $this->response()->item($group, new UserGroupTransformer());

    }
}

