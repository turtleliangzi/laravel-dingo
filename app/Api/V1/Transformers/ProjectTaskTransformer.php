<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectTask;

class ProjectTaskTransformer extends TransformerAbstract {
    
    public function transform(ProjectTask $task) {
        return [
            'id' => $task['pt_id'],
            'name' => $task['task_name'],
            'etimated_time' => $task['etimated_time'],
            'weight' => $task['task_weight'],
            'priority' => $task['task_priority'],
            'type' => $task['task_type'],
            'difficulty' => $task['task_difficulty'],
            'status' => $task['progress_status'],
            'desc' => $task['task_desc'],
            'executor' => $task['name'],
            'reminder' => $task['name'],
            'start_time' => $task['start_time'],
            'end_time' => $task['end_time'],
            'project_name' => $task['project_name'],
        ];
    }
}


