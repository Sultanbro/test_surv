<?php

namespace Eddir\Messenger\database\factories;

use App\Models\MessengerChat as Chat;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MessengerMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        return [
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
            'chat_id' => Chat::pluck('id')->random(),
            'sender_id' => User::pluck('id')->random(),
            'body' => $this->faker->text,
            'read' => $this->faker->boolean,
            // deleted as random from array of booleans, which is contains 5 times true and 1 times false
            'deleted' => $this->faker->randomElement([true, false, false, false, false]),
        ];
    }
}
