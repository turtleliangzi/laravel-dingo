<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Department;

class DepartmentTransformer extends TransformerAbstract {
    
    public function transform(Department $department) {
        return [
            'name' => $department['department_name'],
            'desc' => $department['department_desc'],
            'id' => $department['department_id'],
        ];
    }
}


