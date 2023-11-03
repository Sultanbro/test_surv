<?php

namespace Database\Factories\Analytics;

use App\Models\Analytics\Activity;
use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        $group = ProfileGroup::factory()->create();
        $user = User::factory()->create();
        return [
            'name' => $this->faker->name,
            'group_id' => $group->getKey(),
            'daily_plan' => $this->faker->text,
            'plan_unit' => $this->faker->unixTime,
            'unit' => "kzt",
            'ud_ves' => $this->faker->name,
            'share' => $this->faker->name,
            'method' => $this->faker->name,
            'view' => $this->faker->name,
            'source' => $this->faker->name,
            'editable' => $this->faker->name,
            'order' => 1,
            'type' => "some type",
            'weekdays' => "[1,2,3,5,]",
            'data' => $this->faker->date,
//            'created_by' => $user->getKey(),
//            'updated_by' => $user->getKey(),
            'common' => $this->faker->name
        ];
    }
}
