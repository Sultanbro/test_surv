<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Stancl\Tenancy\Database\Models\Domain;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $domainName = $this->faker->domainName;
        return [
            'id' => $this->faker->numberBetween(1, 99999)
            , 'domain' => $domainName
            , 'tenant_id' => $domainName
        ];
    }
}
