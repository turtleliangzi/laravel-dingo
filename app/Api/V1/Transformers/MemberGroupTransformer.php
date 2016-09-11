<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class MemberGroupTransformer extends TransformerAbstract {
    
    public function transform(User $memberGroup) {
        return [
            'name' => $memberGroup['name'],
            'email' => $memberGroup['email'],
            'id' => $memberGroup['id'],
            'phone' => $memberGroup['phone'],
            'group_name' => $memberGroup['group_name'],
            'group_id' => $memberGroup['group_id'],
        ];
    }
}


