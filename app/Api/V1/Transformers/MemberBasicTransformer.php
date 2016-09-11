<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\DepartmentMember;

class MemberBasicTransformer extends TransformerAbstract {
    
    public function transform(DepartmentMember $member) {
        return [
            'id' => $member['id'],
            'name' => $member['name'],
            'email' => $member['email'],
            'phone' => $member['phone'],
            'role' => $member['position'],
        ];
    }
}


