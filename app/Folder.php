<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Folder extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'folder_name', 'creator', 'parent', 'project', 'create_time', 'update_time', 'type', 'file_path', 'file_size',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $table = 'folder';
    public $timestamps = false;

    /*
     * 获取项目文件夹
     * @param project_id parent
     */

    protected static function getFolder($project_id, $parent) {
        return Folder::where('project', $project_id)->where('parent', $parent)->get();
    }

    /*
     * 获取当前文件夹和所有父文件夹相关信息
     * @param project_id parent
     */

    protected static function getFolderParent($project_id, $parent) {
        $folders = array();
        $i = 0;
        while ($parent != 0) {
            $folder = Folder::where('project', $project_id)->where('floder_id', $parent)->first();
            $parent = $folder['parent'];
            $folders[$i] = $folder;
            $i++; 
        }
        $length = count($folders);
        $parentFolders = array();
        foreach ($folders as $k => $folder) {
            $i = $length-$k-1;
            $parentFolders[$i] = $folders[$k];
        }

        return $parentFolders;

    }
}
