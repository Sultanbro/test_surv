<?php

namespace App\Models\MorphRelations;

use App\Models\File\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<File> $images
 */
trait HasImages
{
    public function addImage(array $data): Model
    {
        return $this->images()->create($data);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(
            File::class
            , 'fileable'
            , 'fileable_type'
            , 'fileable_id'
            , 'id'
        );
    }

    public function addImages(array $data): array
    {
        $images = [];
        foreach ($data as $image) {
            $images[] = $this->images()->create($image);
        }
        return $images;
    }
}