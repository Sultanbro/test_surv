<?php

namespace App\Service\Custom\Files;

use App\Models\Domain\Image\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasImages
{
    public function images(): MorphMany
    {
        return $this->morphMany(
            Image::class
            , 'imageOwner'
            , 'image_owner_type'
            , 'image_owner_id'
            , 'id'
        );
    }

    public function addImage(string $url): Model
    {
        return $this->images()->create([
            'url' => $url
        ]);
    }

    public function addImages(array $urls): array
    {
        $models = [];
        foreach ($urls as $url) {
            $models [] = $this->addImage($url);
        }
        return $models;
    }
}
