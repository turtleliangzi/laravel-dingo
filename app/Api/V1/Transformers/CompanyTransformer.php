<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Company;

class CompanyTransformer extends TransformerAbstract {
    
    public function transform(Company $company) {
        return [
            'name' => $company['company_name'],
            'english' => $company['english_name'],
            'code' => $company['company_code'],
            'founder' => $company['founder'],
        ];
    }
}


