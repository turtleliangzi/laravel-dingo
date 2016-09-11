<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DepartmentMember extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'department'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'department_member';


    /*
    *新增部门-用户记录
    */
    protected static function add($data){
      return DepartmentMember::insert($data);
    }

    /*
     * 通过用户id获取所在部门信息
     * @param user
     */
    protected static function findByUser($user) {
        return DepartmentMember::join('departments', 'department', '=', 'department_id')->where('user', $user)->get();
    }

    /*
     * 通过成员id和部门id判断该成员是否在该部门下
     * @param uid, department_id
     */

    protected static function findUser($uid, $department_id) {
        return DepartmentMember::where('user', $uid)->where('department', $department_id)->first();
    }

    /*
     * 获取部门下所有成员信息
     * @param department_id
     */

    protected static function getMemberAll($department_id, $search) {
        if (empty($search) || $search === 'null') {
            return DepartmentMember::join('users', 'department_member.user', '=', 'id')->where('department', $department_id)->get();
        } else {
            $emailMode = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
            if (is_numeric($search)) {
                return DepartmentMember::join('users', 'department_member.user', '=', 'id')->where('department', $department_id)->where('phone', 'like', '%'.$search.'%')->get();

            } else if (preg_match($emailMode, $search)) {
                return DepartmentMember::join('users', 'department_member.user', '=', 'id')->where('department', $department_id)->where('email', 'like', '%'.$search.'%')->get();

            } else {
                return DepartmentMember::join('users', 'department_member.user', '=', 'id')->where('department', $department_id)->where('name', 'like', '%'.$search.'%')->get();
            }
        }
    }

}
