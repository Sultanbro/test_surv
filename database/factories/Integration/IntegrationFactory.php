<?php

namespace Database\Factories\Integration;

use App\Models\Integration\Integration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Integration>
 */
class IntegrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference' => 'u-call',
            'data' => json_encode([
                'app_id'=>'45445',
                'api_key'=>'sdsds5d4s54d5s4d5s4'
            ])
        ];
    }
}
