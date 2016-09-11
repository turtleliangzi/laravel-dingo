<?php

namespace App\Api\V1\Controllers\Folder;

use App\Api\V1\Controllers\BaseController;
use App\Project;
use App\Folder;
use DB;
use JWTAuth;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\FolderTransformer;


class FolderController extends BaseController {

    /** 
     * @SWG\Post(
     * path="/folder/add",
     * summary="创建文件夹",
     * tags={"Folders"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="folder_name", in="query", required=true, description="文件夹名称", type="string"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="parent", in="query", required=true, description="父文件夹id", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="创建文件夹成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="创建文件夹失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function add(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $folder = array(
            'folder_name' => htmlspecialchars($request->get('folder_name')),
            'project' => intval($request->get('project_id')),
            'parent' => intval($request->get('parent')), 
            'create_time' => date("Y-m-d H:m:s"), 
            'update_time' => date("Y-m-d H:m:s"), 
            'creator' => $user['name'],
        );
        $company = $user['company'];
        if (empty($folder['folder_name'])) {
            return $this->errorResponse("文件夹名称不能为空", 406);
        }
        if (empty($folder['project'])) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($folder['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再该公司内", 406);
        }

        $result = Folder::create($folder);
        if (!empty($result)) {
            return $this->successResponse("创建文件夹成功", 200);
        } else {
            return $this->errorResponse("创建文件夹失败", 500);
        }

    }
    /** 
     * @SWG\Get(
     * path="/folder/get/top/{project_id}/{parent}",
     * summary="获取项目一级文件夹",
     * tags={"Folders"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="parent", in="path", required=true, description="父目录id", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取项目一级文件夹成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取项目一级文件夹失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getFolder($project_id, $parent) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);
        $parent = intval($parent);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再该公司内", 406);
        }

        $folders = Folder::getFolder($project_id, $parent);
        return $this->response->collection($folders, new FolderTransformer());

    }
    /** 
     * @SWG\Get(
     * path="/folder/get/parent/{project_id}/{parent}",
     * summary="获取当前文件夹名称和所有父文件夹名称",
     * tags={"Folders"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="project_id", in="path", required=true, description="项目id", type="integer"),
     * @SWG\Parameter(name="parent", in="path", required=true, description="父目录id", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取文件夹成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取文件夹失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getFolderParent($project_id, $parent) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $project_id = intval($project_id);
        $parent = intval($parent);
        if (empty($project_id)) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        $project = Project::getById($project_id);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再该公司内", 406);
        }

        $folders = Folder::getFolderParent($project_id, $parent);
        $data = array(
            'data' => $folders,
        );
        return response()->json($data);

    }
    /** 
     * @SWG\Post(
     * path="/folder/upload/file",
     * summary="上传文件",
     * tags={"Folders"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="folder_name", in="query", required=true, description="文件名称", type="string"),
     * @SWG\Parameter(name="file_size", in="query", required=true, description="文件大小", type="string"),
     * @SWG\Parameter(name="parent", in="query", required=true, description="父目录id", type="integer"),
     * @SWG\Parameter(name="project_id", in="query", required=true, description="项目id", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户不存在"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="上传文件成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="上传文件失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function uploadFile(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return $this->errorResponse("用户没找到", 404);
        }
        $company = $user['company'];
        $file = array (
            'folder_name' => htmlspecialchars($request->get('folder_name')),
            'file_size' => htmlspecialchars($request->get('file_size')),
            'creator' => $user['name'],
            'project' => intval($request->get('project_id')),
            'parent' => intval($request->get('parent')),
            'create_time' => date("Y-m-d H:m:s"),
            'update_time' => date("Y-m-d H:m:s"),
        );
        $file['file_path'] = '/uploads/'.$file['folder_name'];
        $file['type'] = 1;
        if (empty($file['project'])) {
            return $this->errorResponse("项目id不能为空", 406);
        }
        if (empty($file['folder_name'])) {
            return $this->errorResponse("文件名称不能为空", 406);
        }
        if (empty($file['file_size'])) {
            return $this->errorResponse("文件大小不能为空", 406);
        }
        $project = Project::getById($file['project']);
        if (empty($project)) {
            return $this->errorResponse("该项目不存在", 406);
        }
        if ($project['company'] != $company) {
            return $this->errorResponse("该项目不再该公司内", 406);
        }

        $result = Folder::create($file);
        if ($result) {
            return $this->successResponse("上传文件成功", 200);
        } else {
            return $this->errorResponse("上传文件失败", 500);
        }

    }

}
