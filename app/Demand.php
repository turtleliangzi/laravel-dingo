<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Demand extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'demand_title', 'demand_desc', 'project',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'demands';

    /*
     * 获取待批准/已批准/未批准的需求
     * @param project, kind
     */

    protected static function getAll($project, $kind) {
        return Demand::where('project', 1)->where('demand_status', $kind)->get();
    }
}
