<?php

namespace Database\Factories\File;

use App\Models\File\File;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    public function definition(): array
    {
        $profileGroup = ProfileGroup::factory()->create();

        return [
            'fileable_id' => $profileGroup->id,
            'fileable_type' => ProfileGroup::class,
            'original_name' => 'test name',
            'local_name' => 'locale name',
            'url' => $this->faker->imageUrl,
            'extension' => 'pdf'
        ];
    }
}
