<?php

namespace App\Http\Resources\News\Files;

use App\Models\News\File;
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
            'extension' => $this->resource->extension,
            'url' => asset($this->resource->url),
        ];
    }
}
