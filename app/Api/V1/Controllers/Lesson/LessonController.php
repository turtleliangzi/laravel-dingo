<?php

namespace App\Api\V1\Controllers\Lesson;

use App\Api\V1\Controllers\BaseController;
use App\Lesson;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\LessonTransformer;


class LessonController extends BaseController {

    /**
     * @SWG\Get(
     *   path="/lessons/all",
     *   summary="显示所有教程",
     *   tags={"Lessons"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="all lessons"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */

    public function show() {
        $lessons = Lesson::all();
        return $this->response->collection($lessons, new LessonTransformer);
    }

    /**
     * @SWG\Get(
     *   path="/lessons/one/{id}",
     *   summary="显示单个教程",
     *   tags={"Lessons"},
     *   @SWG\Parameter(name="id", in="path", required=true, description="id", type="string"),
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="one lessons"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function one($id) {
        $id = intval($id);
        $lesson = Lesson::find($id);
        if (empty($lesson)) {
            return $this->response->errorNotFound('lesson not found');
        }
        return $this->response->item($lesson, new LessonTransformer);
    }

}
