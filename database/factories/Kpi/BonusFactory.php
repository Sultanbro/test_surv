<?php

namespace Database\Factories\Kpi;

use Faker\Factory as Faker;
use App\Models\Kpi\Bonus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bonus>
 */
class BonusFactory extends Factory
{
    protected $model = Bonus::class;
    /**
     * Фабрика для модели Bonus.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = Faker::create();
        $targetableId = $faker->randomDigit;
        $user         = $faker->randomDigit;

        return [
            'targetable_id'     => $targetableId,
            'targetable_type'   => array_random([1, 2, 3]),
            'title'             => $faker->word,
            'sum'               => $faker->randomDigit,
            'group_id'          => $targetableId,
            'activity_id'       => $faker->randomDigit,
            'unit'              => array_random(['first', 'all']),
            'quantity'          => $faker->randomDigit,
            'daypart'           => $faker->numberBetween(0,2),
            'text'              => $faker->word,
            'created_by'        => $user,
            'updated_by'        => $user
        ];
    }
}
