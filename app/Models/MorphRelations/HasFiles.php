<?php

namespace App\Models\MorphRelations;

use App\Models\File\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<File> $files
 */
trait HasFiles
{
    public function files(): MorphMany
    {
        return $this->morphMany(
            File::class
            , 'fileable'
            , 'fileable_type'
            , 'fileable_id'
            , 'id'
        );
    }

    public function addFile(array $data): Model
    {
        return $this->files()->create($data);
    }
}