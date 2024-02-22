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
            'local_name' => $this->resource->local_name,
            'extension' => $this->resource->extension,
            'url' => $this->resource->path,
            'signed' => $this->when(!is_null($this->resource->signed), $this->resource->signed),
            'created_at' => $this->resource->created_at,
        ];
    }
}
