<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*
     * 通过邮箱获取用户
     * @pram email
     */

    protected static function  findUserEmail($email) {
        return  User::where('email', $email)->first();
    }

    /*
     * 通过公司和姓名判断用户是否存在
     * @param compnay, name
     */

    protected static function findUserName($company, $name) {
        return User::where('company', $company)->where('name', $name)->first();
    }

    /*
     * 获取公司成员信息
     * @param compnay
     */

    protected static function companyMember($company, $search) {
        if ($search === 'null') {
            return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->get();

        } else {
            $emailMode = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
            if (is_numeric($search)) {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('phone', 'like', '%'.$search.'%')->get();

            } else if (preg_match($emailMode, $search)) {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('email', 'like', '%'.$search.'%')->get();

            } else {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('name', 'like', '%'.$search.'%')->orWhere('department_name', 'like', '%'.$search.'%')->orWhere('role', 'like', '%'.$search.'%')->get();

            }
        }
    }

    /*
     * 获取用户及用户组
     * @param compnay
     */

    protected static function getMemberGroup($company) {
        return User::join('user_group', 'user_group', '=', 'group_id')->where('users.company', $company)->get();
    }
    /*
     * 获取单个用户及用户组信息
     * @param uid
     */

    protected static function getUserGroup($uid) {
        return User::join('user_group', 'user_group', '=', 'group_id')->where('id', $uid)->first();
    }

    /*
     * 更新用户组
     * @param uid, group_id
     */
    protected static function changeUserGroup($uid, $group_id) {
        return User::where('id', $uid)->update(array("user_group"=>$group_id));
    }

    /*
    *添加用户信息
    * @author cck
    * create_time 2016-08-08
    */
    protected static function addMember($memberInfo){
      return User::insertGetId($memberInfo);
    }

    /*
    *根据id获取用户信息
    * @author cck
    * create_time 2016-08-08
    */
    public static function getById($id){
      return User::where('id',$id)->first();
    }
    /*
    *根据id更改登录密码
    * @author yjf
    * create_time 2016-08-08
    */
    public static function changePassword($id,$password){
        return User::where('id',$id)->update(array('password'=>$password));
    }
}
