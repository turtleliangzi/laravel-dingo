<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class RoleType extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_type', 'role_code', 'role_desc', 'grade',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'role_type';

    /*
     * 通过grade获取角色类型
     * @param grade
     */
    
    protected static function getRoleType($grade) {
        return RoleType::where('grade', $grade)->get();
    }

    /*
     * 通过id 获取角色类型
     * @param id
     */

    protected static function getTypeById($id) {
        return RoleType::where('rt_id', $id)->first();
    }

}
