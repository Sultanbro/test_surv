<?php

namespace Tests\Feature;

use App\Events\Event;
use App\Models\Analytics\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

class ActivityTest extends TestCase
{
    /**
     * Получить активность
     */
    // public function test_get_activity(): void
    // {
    //     $this->withoutExceptionHandling();
    //     $this->withoutMiddleware();

    //     $response = $this->json('GET','/activities/get', [
    //         'id' => 4
    //     ]);

    //     $response->assertStatus(200)->assertSee([
    //             'id'              => '',
    //             'name'            => '',
    //             'group_id'        => '',
    //             'daily_plan'      => '',
    //             'share'           => '',
    //             'unit'            => '',
    //             'method'          => '',
    //             'view'            => '',
    //             'source'          => '',
    //             'editable'        => 1,
    //             'order'           => 1,
    //             'weekdays'        => 5,
    // //     ]);
    // }

    public function test_save_activity()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();

        $response = $this->json('POST','/activities/save', [
            'name'            => $faker->word,
            'group_id'        => $faker->numberBetween(1, 100),
            'daily_plan'      => $faker->numberBetween(1, 100),
            'share'           => $faker->numberBetween(1, 100),
            'unit'            => '',
            'method'          => $faker->numberBetween(1, 6),
            'view'            => $faker->numberBetween(0, 6),
            'source'          => $faker->numberBetween(0, 3),
            'editable'        => 1,
            'order'           => 1,
            'weekdays'        => 5,
        ]);

        $response->assertStatus(200)->assertSee([
            'status',
            'message'
        ]);
    }

    public function test_update_activity()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();
        Event::dispatch();
        $response = $this->json('PUT','/activities/update', [
            "id"    => rand(0, 5),
            "title" => $faker->word,
        ]);

        $response->assertStatus(200)->assertSuccessful()->assertSee([
            'status',
            'message'
        ]);
    }

    public function test_delete_activity()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('DELETE','/activities/delete', [
            "id"         => rand(1, 10),
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }
}
