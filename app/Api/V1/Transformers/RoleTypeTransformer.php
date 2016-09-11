<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\RoleType;

class RoleTypeTransformer extends TransformerAbstract {
    
    public function transform(RoleType $type) {
        return [
            'type' => $type['role_type'],
            'desc' => $type['role_desc'],
            'code' => $type['role_code'],
            'id' => $type['rt_id'],
        ];
    }
}


