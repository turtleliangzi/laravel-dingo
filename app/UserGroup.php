<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserGroup extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_name', 'permission', 'company', 'create_time', 'update_time'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'user_group';
    public $timestamps = false;

    /*
     * 添加用户组
     * @param group
     */

    protected static function addGroup($group) {
        return UserGroup::insertGetId($group);
    }

    /*
     * 获取公司下的所有用户组
     * @param company
     */

    protected static function getAllGroup($company) {
        return UserGroup::where('company', $company)->get();
    }

    /*
     * 通过用户组名获取用户组
     * @param name, company
     */

    protected static function getGroupByName($name, $company) {
        return UserGroup::where('group_name', $name)->where('company', $company)->first();
    }

    /*
     * 通过group_id获取用户组
     * @param group_id
     */
    protected static function getGroupById($group_id) {
        return UserGroup::where('group_id', $group_id)->first();
    }

}
