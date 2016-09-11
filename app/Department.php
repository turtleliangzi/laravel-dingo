<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Department extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_name', 'department_desc', 'department_creator', 'company',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'departments';

    /*
     * 通过部门id获取部门信息
     * @param deparment_id
     */

    protected static function findById($department_id) {
        return Department::where('department_id', $department_id)->first();
    }


    /*
    *根据dingding_id获取部门id
    */
    protected static function findByDingId($dingId){
      return Department::where('dingding_id', $dingId)->first();
    }


    /*
     * 通过用户id获取部门id
     * @param user
     */
    protected static function findByUser($user) {
        return Department::where('user', $user)->get();
    }

    /*
     * 获取公司下所有部门信息
     * @param company
     */

    protected static function findAllByCompany($company) {
        return Department::where('company', $company)->get();
    }

    /*
     * 获取部门总数
     * @param company
     */

    protected static function departmentCount($company) {
        return Department::where('company', $company)->count();
    }


    /*
    *添加或更新从叮叮获取的某一公司的部门信息
    * @author cck
    * create_time 2016-08-06
    */
    protected static function addAndUpdateDepartment($depInfo,$company_code){
      $record = Department::where('company',$company_code)->where('dingding_id',$depInfo['id'])->first();
      $rs = true;
      if(empty($record)){
        $data['department_name'] = $depInfo['name'];
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['company'] = $company_code;
        $data['dingding_id'] = $depInfo['id'];
        $data['deptManagerUseridList'] = $depInfo['deptManagerUseridList'];
        $rs = Department::insert($data);
      }else if($record['deptManagerUseridList'] != $depInfo['deptManagerUseridList']||$record['department_name']!=$depInfo['name']){
         $rs = Department::where('company',$company_code)->where('dingding_id',$depInfo['id'])
         ->update(['updated_at' => date('Y-m-d H:i:s', time()),'deptManagerUseridList'=>$depInfo['deptManagerUseridList']]);
      }
      return $rs;
    }

}
