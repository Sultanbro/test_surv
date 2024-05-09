<?php

namespace Database\Factories;

use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileGroup>
 */
class ProfileGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'users' => "[]", // Customize this based on your needs
            'work_start' => $this->faker->time,
            'work_end' => $this->faker->time,
            'workdays' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'editors_id' => $this->faker->numberBetween(1, 10), // Replace with appropriate range
            'required' => $this->faker->numberBetween(1, 5), // Replace with appropriate range
            'provided' => $this->faker->numberBetween(1, 5), // Replace with appropriate range
            'head_id' => $this->faker->numberBetween(1, 10), // Replace with appropriate range
            'bp_link' => $this->faker->url,
            'zoom_link' => $this->faker->url,
            'checktime' => $this->faker->time,
            'checktime_users' => "[]", // Customize this based on your needs
            'salary_approved' => $this->faker->numberBetween(1000, 5000), // Replace with appropriate range
            'salary_approved_by' => $this->faker->name,
            'salary_approved_date' => $this->faker->dateTimeThisMonth,
            'active' => $this->faker->boolean,
            'payment_terms' => $this->faker->sentence,
            'has_analytics' => $this->faker->boolean,
            'editable_time' => $this->faker->randomElement([0, 1]),
            'time_address' => $this->faker->randomElement([-1, 0]),
            'paid_internship' => $this->faker->randomElement([0, 1]),
            'rentability_max' => $this->faker->numberBetween(50, 100), // Replace with appropriate range
            'show_payment_terms' => $this->faker->randomElement([0, 1]),
            'archived_date' => $this->faker->dateTimeThisYear,
            'work_chart_id' => $this->faker->numberBetween(1, 10), // Replace with appropriate range
            'archive_utility' => $this->faker->randomElement([0, 1]),
            'switch_utility' => $this->faker->randomElement([0, 1]),
            'switch_proceeds' => $this->faker->randomElement([0, 1]),
            'switch_rentability' => $this->faker->randomElement([0, 1]),
        ];
    }
}
