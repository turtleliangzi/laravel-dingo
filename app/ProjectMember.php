<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProjectMember extends Model {

    protected $table = 'project_member';

    /*
     * 新增项目成员
     * @param member
     */

    protected static function addMember($member) {
        return ProjectMember::insert($member);
    }

    /*
     * 获取项目成员
     * @param project_id
     */

    protected static function getMember($project_id) {
        return ProjectMember::join('users', 'id', '=', 'member')->where('project', $project_id)->get();
    }

    /*
     * 通过member和project获取成员
     * @param member, project
     */

    protected static function getByProject($member, $project) {
        return ProjectMember::where('member', $member)->where('project', $project)->first();
    }

    /*
     * 获取公司项目成员
     * @author turtle
     * create_time 2016-08-17
     */

    protected static function getProjectMember($company) {
        return DB::table('project_member')->join('projects', 'project_id', '=', 'project')->join('users', 'id', '=', 'member')->where('projects.company', $company)->where('approve_status', 3)->where('project_role', '<>', '产品经理')->distinct()->select('member', 'dingding_id')->get();
    }
}
