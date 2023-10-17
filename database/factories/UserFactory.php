<?php

namespace Database\Factories;

use DB;
use Illuminate\Database\Eloquent\Factories\Factory;

// Replace 'App\Models\User' with the actual User model namespace

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // email	phone	name	last_name	password	remember_token	birthday	login_at	city	lead	country	balance	currency
        $central = [
            'name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // Password placeholder
            'remember_token' => $this->faker->sha1,
            'birthday' => $this->faker->date('Y-m-d', '-20 years'),
            'city' => $this->faker->city,
            'currency' => $this->faker->currencyCode
        ];
        $tenant = [];
        if (DB::connection()->getName() == 'tenant') {
            $tenant = [
                'full_time' => $this->faker->boolean(),
                'user_type' => $this->faker->randomElement(['Admin', 'User', 'Moderator']),
                'address' => $this->faker->address,
                'description' => $this->faker->paragraph,
                'timezone' => $this->faker->timezone,
                'segment' => $this->faker->randomElement(['Segment A', 'Segment B', 'Segment C']),
                'working_country' => $this->faker->country,
                'working_city' => $this->faker->city,
                'work_start' => $this->faker->time(),
                'work_end' => $this->faker->time(),
                'read_corp_book_at' => $this->faker->dateTimeThisYear,
                'has_noti' => $this->faker->boolean(),
                'notified_at' => $this->faker->dateTimeThisYear,
                'role_id' => $this->faker->randomNumber(4),
                'is_admin' => $this->faker->boolean(),
                'groups_all' => $this->faker->words(3, true),
                'applied_at' => $this->faker->dateTimeThisYear,
                'weekdays' => $this->faker->randomElement(['Mon', 'Tue', 'Wed', 'Thu', 'Fri']),
                'img_url' => $this->faker->imageUrl(),
                'headphones_sum' => $this->faker->randomFloat(2, 10, 500),
                'phone_1' => $this->faker->phoneNumber,
                'phone_2' => $this->faker->phoneNumber,
                'phone_3' => $this->faker->phoneNumber,
                'phone_4' => $this->faker->phoneNumber,
                'work_chart_id' => $this->faker->randomNumber(4),
                'referrer_id' => null,
            ];
        }
        return array_merge($central, $tenant);
    }
}
