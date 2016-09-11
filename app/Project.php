<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Project extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_name', 'project_desc', 'project_type', 'company', 'project_range', 'project_applicant', 'etimated_time', 'create_time',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'projects';

    /*
     * 获取未审核的项目
     * @param company
     */

    protected static function unauditedProject($company, $kind) {
        if ($kind <= 1) {
            return Project::where('company', $company)->where('approve_status', $kind)->get();

        } elseif ($kind >= 2) {
            return Project::where('company', $company)->where('approve_status', '>=', $kind)->get();
        
        }
    }

    /*
     * 通过id 获取项目
     * @param id
     */

    protected static function getById($id) {
        return Project::where('project_id', $id)->first();
    }

    /*
     * 更新审核状态
     * @param id, status
     */

    protected static function updateAuditStatus($id, $update) {
        return Project::where('project_id', $id)->update($update);
    }

    /*
     * 获取我的项目
     * @param uid
     */

    protected static function getMyProject($uid) {
        return Project::join('project_member', 'project_id', '=', 'project')->where('member', $uid)->get();
    }

    /*
     * 更新项目状态
     * @param data project_id
     */

    protected static function updateStatus($data, $project_id) {
        return Project::where('project_id', $project_id)->update($data);
    }

    /*
     * 获取公司下所有通过并分配完的项目
     * @param company
     */
    protected static function getAllProject($company) {
        return Project::where('company', $company)->where('approve_status', '>=', 2)->get();
    }
    /*
     * 获取公司下所有项目状态
     * @author yjf
     * create_time 2016-08-09
     */
    protected static function checkStatus($company,$status){
        return Project::where(['company'=>$company,'approve_status'=>$status])->count();
    }
}
