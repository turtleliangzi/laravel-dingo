<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Department;

class Company extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name', 'english_name', 'company_code', 'founder',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'companies';

    /*
     * 通过公司代号查找公司
     * @param code
     */

    protected static function findCompany($code) {
        return Company::where('company_code', $code)->first();
    }

    /*
     * 获取公司成员总数
     * @param company
     */

    protected static function  memberCount($company) {
        return DB::table('users')->where('company', $company)->count();
    }


    /*
    *通过部门的dingding_id查找公司
    * @author cck
    * create_time 2016-08-06
    */
    protected static function findCompanyBydepDingId($depDing_id){
      return  Department::where('dingding_id',$depDing_id)->first();
    }
}
