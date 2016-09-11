<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProjectTask extends Model {

    protected $table = 'project_task';
    public $timestamps = false;

    /*
     * 新增任务
     * @param task
     */

    protected static function addTask($task) {
        return ProjectTask::insertGetId($task);
    }

    /*
     * 通过id获取任务
     * @param id
     */

    protected static function getById($id) {
        return ProjectTask::leftJoin('users', 'executor', '=', 'id')->where('pt_id', $id)->first();
    }

    /*
     * 获取某阶段目标下的状态任务
     * @param goal, status
     */

    protected static function getTask($goal, $status) {
        if ($status == 0) {
            return ProjectTask::leftJoin('users', 'reminder', '=', 'id')->where('goal', $goal)->where('progress_status', $status)->get();
        
        } else {
            return ProjectTask::leftJoin('users', 'executor', '=', 'id')->where('goal', $goal)->where('progress_status', $status)->get();
        
        }
    }

    /*
    *获取任务的部分信息
    */
    protected static function getSomeTask($company, $status) {
        /* return DB::select('select task_name,etimated_time,executor,start_time as remain_time from project_task where goal=:goal and progress_status=:progress_status', */
        /* ['goal' => $goal,'progress_status'=>$status]); */
        if ($status > 0) {
            return ProjectTask::join('users', 'executor', '=', 'id')->join('project_goal', 'goal', '=', 'pg_id')->join('projects', 'project', '=', 'project_id')->where('projects.company', $company)->where('project_task.progress_status', $status)->select('users.name', 'project_task.*', 'project_name')->get();
        
        } elseif ($status == 0) {
            return ProjectTask::join('users', 'reminder', '=', 'id')->join('project_goal', 'goal', '=', 'pg_id')->join('projects', 'project', '=', 'project_id')->where('projects.company', $company)->where('project_task.progress_status', $status)->select('users.name', 'project_task.*', 'project_name')->get();
        
        }
    }

    /*
     *  更新任务状态
     *  @param data, task_id
     */
    protected static function updateStatus($data, $task_id) {
        return ProjectTask::where('pt_id', $task_id)->update($data);
    }

    /*
     * 获取项目成员正在进行的任务
     * @author turtle
     * create_time 2016-08-17
     */

    protected static function getDoingTask($member) {
        return ProjectTask::where('executor', $member)->where('progress_status', 1)->get();
    }
}
