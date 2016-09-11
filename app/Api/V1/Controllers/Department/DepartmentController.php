<?php

namespace App\Api\V1\Controllers\Department;

use App\Api\V1\Controllers\BaseController;
use App\Department;
use App\Company;
use App\User;
use App\UserGroup;
use App\DepartmentMember;
use App\RoleType;
use App\Dingding;
use App\Sendmail;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\DepartmentTransformer;
use App\Api\V1\Transformers\DepartmentMemberTransformer;
use App\Api\V1\Transformers\MemberBasicTransformer;


class DepartmentController extends BaseController {

    /**
     * @SWG\Post(
     * path="/department/add",
     * summary="新建部门",
     * tags={"Departments"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="department_name", in="query", required=true, description="公司名", type="string"),
     * @SWG\Parameter(name="department_desc", in="query", required=false, description="部门描述", type="string"),
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
     *   description="创建部门成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="创建部门失败"
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
        $department = array(
            'department_name' => htmlspecialchars($request->get('department_name')),
            'department_desc' => htmlspecialchars($request->get('department_desc')),
            'department_creator' => $user['name'],
            'company' => $user['company'],
        );
        if (empty($department['department_name'])) {
            return $this->errorResponse("部门名不能为空", 406);
        }

        $result = Department::create($department);
        if (!empty($result)) {
            return $this->successResponse("创建部门成功", 200);
        } else {
            return $this->errorResponse("创建部门失败", 500);
        }

    }


    /**
     * @SWG\Get(
     * path="/department/one/info/{department_id}",
     * summary="单个部门信息",
     * tags={"Departments"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="department_id", in="path", required=true, description="部门id", type="integer"),
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
     *   description="获取单个部门信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取单个部门信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function oneInfo($department_id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $department_id = (integer)($department_id);
        $company = $user['company'];
        if (empty($department_id)) {
            return $this->errorResponse("部门id不能为空", 406);
        }

        $department = Department::findById($department_id);
        if ($department['company'] != $company) {
            return $this->errorResponse("对不起，非法访问", 406);
        }
        return $this->response->item($department, new DepartmentTransformer);

    }

    /**
     * @SWG\Post(
     * path="/department/add/member",
     * summary="部门新增成员",
     * tags={"Departments"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="department", in="query", required=true, description="部门id", type="integer"),
     * @SWG\Parameter(name="email", in="query", required=true, description="成员邮箱", type="integer"),
     * @SWG\Parameter(name="role_type", in="query", required=true, description="角色类型", type="integer"),
     * @SWG\Parameter(name="role", in="query", required=true, description="角色名", type="string"),
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
     *   description="部门新增成员成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="部门新增成员失败"
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
        $department = array(
            'department' => (integer)($request->get('department')),
        );
        $memberRole = array(
            'role_type' => (integer)($request->get('role_type')),
            'role' => htmlspecialchars($request->get('role')),
        );
        $email = htmlspecialchars($request->get('email'));
        $company = $user['company'];

        if (empty($email)) {
            return $this->errorResponse("用户邮箱不能为空", 406);
        }
        if (empty($department['department'])) {
            return $this->errorResponse("部门id不能为空", 406);
        }
        if (empty($memberRole['role_type'])) {
            return $this->errorResponse("角色类型不能为空", 406);
        }
        if (empty($memberRole['role'])) {
            return $this->errorResponse("角色名不能为空", 406);
        }

        $departmentExist = Department::findById($department['department']);
        if (empty($departmentExist)) {
            return $this->errorResponse("公司下的该部门不存在", 406);
        } else {
            if ($departmentExist['company'] != $company) {
                return $this->errorResponse("对不起，非法访问", 406);
            }
        }
        $userInfo = User::findUserEmail($email);
        if (empty($userInfo)) {
            return $this->errorResponse("该用户邮箱不存在", 406);
        }
        $memberExist = DepartmentMember::findUser($userInfo['id'], $department['department']);
        if (!empty($memberExist)) {
            return $this->errorResponse("该成员已在该部门下", 406);
        }
        $roleTypeExist = RoleType::getTypeById($memberRole['role_type']);
        if (empty($roleTypeExist)) {
            return $this->errorResponse("该角色类型不存在", 406);
        }
        $memberRole['user'] = $userInfo['id'];
        $memberRole['create_time'] = date("Y-m-d H:m:s");
        $memberRole['update_time'] = date("Y-m-d H:m:s");
        $department['user'] = $userInfo['id'];
        $department['create_time'] = date("Y-m-d H:i:s");
        $department['update_time'] = date("Y-m-d H:i:s");

        $rs = DB::table('department_member')->insert($department);
        if ($rs) {
            DB::table('member_role')->insert($memberRole);
            DB::table('users')->where('id', $userInfo['id'])->update(array("company"=>$company));
            return $this->successResponse("部门新增成员成功", 200);
        } else {
            return $this->errorResponse("部门新增成员失败", 500);
        }

    }

    /**
     * @SWG\Get(
     * path="/department/all/info",
     * summary="获取公司下所有部门信息",
     * tags={"Departments"},
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
     *   description="获取所有部门信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取所有部门信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function allInfo() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $departments = Department::findAllByCompany($company);
        return $this->response->collection($departments, new DepartmentTransformer);

    }

    /**
     * @SWG\Get(
     * path="/department/member/department",
     * summary="获取用户所在部门",
     * tags={"Departments"},
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
     *   description="获取所有部门信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取所有部门信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function memberDepartment() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $id = $user['id'];

        $departments = DepartmentMember::findByUser($id);
        return $this->response->collection($departments, new DepartmentMemberTransformer);

    }


    /**
     * @SWG\Get(
     * path="/department/member/info/{department_id}/{search}",
     * summary="部门下成员信息",
     * tags={"Departments"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="department_id", in="path", required=true, description="部门id", type="integer"),
     * @SWG\Parameter(name="search", in="path", required=true, description="搜索(搜索全部天null)", type="string"),
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
     *   description="获取部门下成员信息成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取部门下成员信息失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function memberInfo($department_id, $search) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $department_id = (integer)($department_id);
        $company = $user['company'];
        $search = htmlspecialchars($search);
        if (empty($department_id)) {
            return $this->errorResponse("部门id不能为空", 406);
        }

        $department = Department::findById($department_id);
        if ($department['company'] != $company) {
            return $this->errorResponse("对不起，非法访问", 406);
        }

        $memberInfo = DepartmentMember::getMemberAll($department_id, $search);
        return $this->response->collection($memberInfo, new MemberBasicTransformer);

    }
    /**
     * @SWG\Get(
     * path="/department/count",
     * summary="获取公司部门总数",
     * tags={"Departments"},
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
     *   description="获取公司部门总数成功"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function departmentCount() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company_code = $user['company'];
        $count = Department::departmentCount($company_code);
        $data = array (
            'data' => array (
                'count' => $count,
            )
        );
        return response()->json($data);
    }

    /**
     * @SWG\Get(
     * path="/department/getlist",
     * summary="获取部门列表",
     * tags={"Departments"},
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
     *   description="获取部门列表成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取部门列表失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */
    public function getDepList(){
      if (! $user = JWTAuth::parseToken()->authenticate()) {
          return $this->errorResponse("用户没找到", 404);
      }
      $company_code = $user['company'];
      $departments = Dingding::getDepartment();
      $depInfoArr = array();
      $index = 0;
      for($i=0;$i<count($departments['department']);$i++){
          $id = $departments['department'][$i]->id;
          $name = $departments['department'][$i]->name;
          $parentid = $id!=1?$departments['department'][$i]->parentid:0;
          if($id!=1&&$parentid==1){
            $departmentInfo = Dingding::getDepartmentInfo($id);
            $deptManagerUseridList = $departmentInfo['deptManagerUseridList'];
            $depInfo = array(
              "id"=>$id,
              "name"=>$name,
              "deptManagerUseridList"=>$deptManagerUseridList
            );
            $depInfoArr[$index] = $depInfo;
            $index++;
            $rs = Department::addAndUpdateDepartment($depInfo,$company_code);
            if(!$rs)
              return $this->errorResponse("同步部门列表失败", 500);
          }
      }

      // $departments = $depInfoArr;
      // $data = array (
      //     'data' => array (
      //         '$departmentInfos' => $departments,
      //     )
      // );
      // return response()->json($data);
      return $this->successResponse("同步部门列表成功", 200);

    }


    /**
     * @SWG\Get(
     * path="/department/getMemberlist/{department_id}",
     * summary="获取部门成员详情列表",
     * tags={"Departments"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="department_id", in="path", required=true, description="部门id", type="integer"),
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
     *   description="获取部门成员详情列表成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取部门成员详情列表失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */
    public function getDingMemberList($department_id){
      if (! $user = JWTAuth::parseToken()->authenticate()) {
          return $this->errorResponse("用户没找到", 404);
      }
      //通过部门id获取部门钉钉id
      $department = Department::findById($department_id);
      $depDing_id = $department->dingding_id;
      $rs = Dingding::getMemberInfo($depDing_id);
      if(empty($rs)){
        return $this->errorResponse("同步钉钉部门成员失败", 500);
      }
      for($i=0;$i<count($rs['userlist']);$i++){
        $userInfo = $rs['userlist'][$i];
        $data['name'] = $userInfo->name;
        if(empty($userInfo->orgEmail)){
          Dingding::sendTextMessage($userInfo->userid,"您还没有钉邮账号，这将导致你不能使用安纳斯项目管理系统，请联系项目经理为你分配一个钉邮账号");
          continue;
        }else{
          $data['email'] = $userInfo->orgEmail;
        }
        $data['password'] = bcrypt("Anasit777");
        $data['phone'] = $userInfo->mobile;
        $data['company'] = $user['company'];//Company::findCompanyBydepDingId($depDing_id)->company;
        $data['avatar'] = $userInfo->avatar;
        $data['dingding_id'] = $userInfo->userid;
        $data['jobnumber'] = $userInfo->jobnumber;
        $data['position'] =$userInfo->position;
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['user_group'] = UserGroup::getGroupByName("默认组",$data['company'])->group_id; //从user_group表里，由公司id找到默认组id，然后写入uer_group字段

        //将用户信息添加进两张表里
        $returnId = User::addMember($data);
        if($returnId<=0){
              return $this->errorResponse("同步钉钉部门成员失败", 500);
        }
        if($returnId){
          $dm_data['user'] = $returnId;
          $dm_data['department'] = $department_id;
          $dm_data['create_time'] =  date('Y-m-d H:i:s', time());
          DepartmentMember::add($dm_data);
        }

        //向用户发送钉邮和钉钉消息通知其重置密码
        $ms_data['email'] = $userInfo->orgEmail;
        $ms_data['name'] = $userInfo->name;
        $ms_data['content'] = "重置密码";
        Sendmail::sendMail($ms_data, 'email.mailview');
        Dingding::sendTextMessage($userInfo->userid,"       已为您分配了安纳斯项目管理系统的账号和密码。
      账号默认为钉钉邮箱，密码初始化为Anasit777。
      请查看钉邮重置密码");
      }

        return $this->successResponse("同步钉钉部门成员成功", 200);

    }


}
