<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Lesson;

class LessonTransformer extends TransformerAbstract {

    public function transform(Lesson $lesson) {
        return [
            'title' => $lesson['title'],
            'content' => $lesson['body'],
            'is_free' => (boolean)($lesson['free']),
        ];
    }   
}

