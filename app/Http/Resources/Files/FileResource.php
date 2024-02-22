<?php

namespace App\Http\Resources\Files;

use App\Models\File\File;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property File $resource
 */
class FileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'original_name' => $this->resource->original_name,
            'original_path' => $this->resource->original_path,
            'local_name' => $this->resource->local_name,
            'extension' => $this->resource->extension,
            'url' => $this->resource->s3_url,
            'signed_at' => $this->when(!is_null($this->resource->signed_at), $this->resource->signed_at),
        ];
    }
}
