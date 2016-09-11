<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\DepartmentMember;

class DepartmentMemberTransformer extends TransformerAbstract {
    
    public function transform(DepartmentMember $departmentMember) {
        return [
            'name' => $departmentMember['department_name'],
            'desc' => $departmentMember['department_desc'],
            'id' => $departmentMember['department_id'],
        ];
    }
}


