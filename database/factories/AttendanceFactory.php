<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'manager_id' => $this->faker->randomDigit,
            'user_id'    => $this->faker->randomDigit,
            'group_id'   => $this->faker->randomDigit,
            'date'       => $this->faker->date(),
            'comment'    => $this->faker->word
        ];
    }
}
