<?php

namespace App\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Folder;

class FolderTransformer extends TransformerAbstract {
    
    public function transform(Folder $folder) {
        return [
            'id' => $folder['floder_id'],
            'name' => $folder['folder_name'],
            'creator' => $folder['creator'],
            'parent' => $folder['parent'],
            'file_path' => $folder['file_path'],
            'size' => $folder['file_size'],
            'type' => $folder['type'],
            'update_time' => $folder['update_time'],
        ];
    }
}


