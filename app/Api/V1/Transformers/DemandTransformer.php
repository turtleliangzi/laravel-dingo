<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Demand;

class DemandTransformer extends TransformerAbstract {
    
    public function transform(Demand $demand) {
        return [
            'id' => $demand['demand_id'],
            'title' => $demand['demand_title'],
            'desc' => $demand['demand_desc'],
            'demand_status' => $demand['demand_status'],
            'progress_status' => $demand['progress_status'],
        ];
    }
}


