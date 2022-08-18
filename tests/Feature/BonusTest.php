<?php

namespace Tests\Feature;

use App\Events\Event;
use App\Models\Kpi\Bonus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

class BonusTest extends TestCase
{
    /**
     * Получить бонус
     */
    // public function test_get_bonus(): void
    // {
    //     $this->withoutExceptionHandling();
    //     $this->withoutMiddleware();

    //     $response = $this->json('GET','/bonus/get', [
    //         'id' => 4
    //     ]);

    //     $response->assertStatus(200)->assertSee([
    //         "id"                => "",
    //         "targetable_id"     => "",
    //         "targetable_type"   => "",
    //         "activity_id"       => "",
    //         "title"             => "",
    //         "text"              => "",
    //         "deleted_at"        => "",
    //         "created_at"        => "",
    //         "updated_at"        => ""
    //     ]);
    // }

    public function test_save_bonus()
    {
        $this->withoutExceptionHandling(); 
        $this->withoutMiddleware();
        $faker = Faker::create();

        $response = $this->json('POST','/bonus/save', [
            'targetable_id'   => $faker->numberBetween(1, 100),
            'targetable_type' => array_rand(['App\User', 'App\ProfileGroup', 'App\Position']),
            'activity_id'     => $faker->numberBetween(1, 100),
            'group_id'     => $faker->numberBetween(1, 100),
            'title'           => $faker->word,
            'text'            => $faker->word,
            'unit'            => '',
            'quantity'        => $faker->numberBetween(1, 10),
            'daypart'         => $faker->numberBetween(0, 2),
        ]);

        $response->assertStatus(200)->assertSee([
            'status',
            'message'
        ]);
    }

    public function test_update_bonus()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();
        Event::dispatch();
        $response = $this->json('PUT','/bonus/update', [
            "id"    => rand(0, 5),
            "title" => $faker->word,
        ]);

        $response->assertStatus(200)->assertSuccessful()->assertSee([
            'status',
            'message'
        ]);
    }

    public function test_delete_bonus()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('DELETE','/bonus/delete', [
            "id"         => rand(1, 10),
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }
}
