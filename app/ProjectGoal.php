<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProjectGoal extends Model {

    public $timestamps = false;
    protected $table = 'project_goal';

    /*
     * 新增目标
     * @param goal
     */

    protected static function addGoal($goal) {
        return ProjectGoal::insert($goal);
    }

    /*
     * 通过id获取目标
     * @param id
     */

    protected static function getById($id) {
        return ProjectGoal::where('pg_id', $id)->first();
    }

    /*
     * 获取某个项目下的所有目标
     * @param project_id
     */

    protected static function getGoalAll($project_id) {
        return ProjectGoal::where('project', $project_id)->orderBy('goal_order')->get();
    }

    /*
     * 更新目标状态
     * @param data, goal_id
     */
    protected static function updateStatus($data, $goal_id) {
        return ProjectGoal::where('pg_id', $goal_id)->update($data);
    }
}

