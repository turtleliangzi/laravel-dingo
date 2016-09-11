<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserPermissionTransformer extends TransformerAbstract {
    
    public function transform(User $userPermission) {
        return [
            'name' => $userPermission['name'],
            'email' => $userPermission['email'],
            'group_name' => $userPermission['group_name'],
            'permission' => $userPermission['permission'],
        ];
    }
}


