<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\UserGroup;

class UserGroupTransformer extends TransformerAbstract {
    
    public function transform(UserGroup $group) {
        return [
            'name' => $group['group_name'],
            'permission' => $group['permission'],
            'id' => $group['group_id'],
        ];
    }
}


