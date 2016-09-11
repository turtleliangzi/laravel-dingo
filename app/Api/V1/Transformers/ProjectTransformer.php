<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Project;

class ProjectTransformer extends TransformerAbstract {
    
    public function transform(Project $project) {
        return [
            'id' => $project['project_id'],
            'name' => $project['project_name'],
            'type' => $project['project_type'],
            'range' => $project['project_range'],
            'applicanter' => $project['project_applicant'],
            'approve_status' => $project['approve_status'],
            'etimated_time' => $project['etimated_time'],
            'actual_time' => $project['actual_time'],
            'audit_time' => $project['audit_time'],
            'distribute_time' => $project['distribute_time'],
            'start_time' => $project['start_time'],
            'online_time' => $project['online_time'],
            'end_time' => $project['end_time'],
            'create_time' => $project['create_time'],
        ];
    }
}


