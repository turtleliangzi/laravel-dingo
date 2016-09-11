<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectMember;

class ProjectMemberTransformer extends TransformerAbstract {
    
    public function transform(ProjectMember $member) {
        return [
            'id' => $member['id'],
            'name' => $member['name'],
            'email' => $member['email'],
            'avatar' => $member['avatar'],
            'role' => $member['project_role'],
        ];
    }
}


