<?php

namespace App\Api\V1\Controllers\Company;

use App\Api\V1\Controllers\BaseController;
use App\Company;
use App\User;
use App\UserGroup;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\CompanyTransformer;
use App\Api\V1\Transformers\CompanyMemberTransformer;
use Illuminate\Support\Arr;


class CompanyController extends BaseController {

    /** 
     * @SWG\Post(
     * path="/company/add",
     * summary="新建公司",
     * tags={"Companys"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="company_name", in="query", required=true, description="公司名", type="string"),
     * @SWG\Parameter(name="english_name", in="query", required=true, description="英文名", type="string"),
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
     *   description="创建公司成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="创建公司失败"
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
        $company = array(
            'company_name' => htmlspecialchars($request->get('company_name')),
            'english_name' => htmlspecialchars($request->get('english_name')),
            'founder' => (integer)($user['id']), 
        );
        $company['company_code'] = $company['english_name']."-".mt_rand(10000,99999);
        if (empty($company['company_name'])) {
            return $this->errorResponse("公司名不能为空", 406);
        }
        if (empty($company['english_name'])) {
            return $this->errorResponse("英文名不能为空", 406);
        }

        $result = Company::create($company);
        if (!empty($result)) {
            $group = array (
                'company' => $result['company_code'],
                'create_time' => date("Y-m-d H:m:s"),
                'update_time' => date("Y-m-d H:m:s"),
            );
            $group['group_name'] = "超级管理组";
            $permission = array (
                'all' => true,
                'add_department' => false,
                'add_department_member' => false,
                'add_project' => false,
                'audit_project' => false,
                'add_group' => false,
                'assign_permission' => false,
                'check_contacts' => false,
                'check_all_departments' => false,
                'add_demand' => false,
                'check_all_projects' => false,
                'edit_group' => false,
                'delete_group' => false,
            );
            $group['permission'] = json_encode($permission);
            $group_id = UserGroup::addGroup($group);
            $group['group_name'] = "默认组";
            $permission = array(
                'all' => false,
                'add_department' => false,
                'add_department_member' => false,
                'add_project' => false,
                'audit_project' => false,
                'add_group' => false,
                'assign_permission' => false,
                'check_contacts' => false,
                'check_all_departments' => false,
                'add_demand' => false,
                'check_all_projects' => false,
                'edit_group' => false,
                'delete_group' => false,
            );
            $group['permission'] = json_encode($permission);
            UserGroup::create($group);
            $userInfo['company'] = $result['company_code'];
            $userInfo['user_group'] = $group_id;
            DB::table('users')->where('id', $company['founder'])->update($userInfo);
            return $this->successResponse("创建公司成功", 200);
        } else {
            return $this->errorResponse("创建公司失败", 500);
        }

    }


    /** 
     * @SWG\Post(
     * path="/company/join",
     * summary="加入公司",
     * tags={"Companys"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="company_code", in="query", required=true, description="公司代号", type="string"),
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
     *   description="加入公司成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="加入公司失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function join(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = array(
            'company' => htmlspecialchars($request->get('company_code'))
        );
        $uid = (integer)($user['id']);
        if (empty($company['company'])) {
            return $this->errorResponse("公司代号不能为空", 406);
        }

        $companyExist = Company::findCompany($company['company']);
        if (empty($companyExist)) {
            return $this->errorResponse("该公司不存在", 406);
        }


        $result = DB::table('users')->where('id', $uid)->update($company);
        if ($result) {
            $user_group = UserGroup::getGroupByName("默认组", $company);
            $userInfo['user_group'] = intval($user_group['group_id']);
            DB::table('users')->where('id', $user['id'])->update($userInfo);
            return $this->successResponse("加入公司成功", 200);
        } else {
            return $this->errorResponse("加入公司失败", 500);
        }

    }


    /** 
     * @SWG\Get(
     * path="/company/info",
     * summary="获取公司信息",
     * tags={"Companys"},
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
     *   description="获取公司信息成功"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function info() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company_code = $user['company'];
        $companyInfo = Company::findCompany($company_code);
        $companyInfo['founder'] = User::find($companyInfo['founder'])['name'];
        return $this->response->item($companyInfo, new CompanyTransformer);
    }
    /** 
     * @SWG\Get(
     * path="/company/member/count",
     * summary="获取公司成员总数",
     * tags={"Companys"},
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
     *   description="获取公司成员总数成功"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function memberCount() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company_code = $user['company'];
        $count = Company::memberCount($company_code);
        $data = array (
            'data' => array (
                'count' => $count,
            )
        );
        return response()->json($data);
    }
    /** 
     * @SWG\Get(
     * path="/company/member/info/{search}",
     * summary="获取公司成员信息",
     * tags={"Companys"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="search", in="path", required=true, description="搜索条件,搜索为空请填null", type="string"),
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
     *   description="获取公司成员信息成功"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function memberInfo($search) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $search = htmlspecialchars($search);
        if (empty($search)) {
            $search = 'null';
        }
        $company_code = $user['company'];
        $memberInfo = User::companyMember($company_code, $search);
        return $this->response->collection($memberInfo, new CompanyMemberTransformer);
    }
}
