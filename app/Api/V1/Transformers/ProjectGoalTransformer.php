<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectGoal;

class ProjectGoalTransformer extends TransformerAbstract {
    
    public function transform(ProjectGoal $goal) {
        return [
            'id' => $goal['pg_id'],
            'weight' => $goal['goal_weight'],
            'start_time' => $goal['start_time'],
            'etimated_end_time' => $goal['etimated_end_time'],
            'order' => $goal['goal_order'],
            'name' => $goal['goal_name'],
            'status' => $goal['progress_status'],
            'actual_end_time' => $goal['actual_end_time'],
        ];
    }
}


