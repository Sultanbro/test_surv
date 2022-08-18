<?php

namespace Tests\Feature;

use App\Events\Event;
use App\Models\QuartalPremium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

class QuartalPremiumTest extends TestCase
{
    /**
     * Получить Квартальную премию.
     *
     * @return void
     */
    public function testGetQuartalPremium(): void
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('GET','/quartal-premium/get', [
            'id' => 4
        ]);

        $response->assertStatus(200)->assertSee([
            "id"                => "",
            "targetable_id"     => "",
            "targetable_type"   => "",
            "activity_id"       => "",
            "title"             => "",
            "text"              => "",
            "plan"              => "",
            "from"              => "",
            "to"                => "",
            "deleted_at"        => "",
            "created_at"        => "",
            "updated_at"        => ""
        ]);
    }

    public function testSaveQP()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();

        $response = $this->json('POST','/quartal-premium/save', [
            "targetable_id"      => $faker->randomDigit,
            "targetable_type"    => array_rand(['App\User', 'App\ProfileGroup', 'App\Position']),
            "activity_id"       => $faker->randomDigit,
            "title"             => $faker->word,
            "text"              => $faker->word,
            "plan"              => $faker->randomNumber(),
            "from"              => $faker->date(),
            "to"                => $faker->date(),
        ]);

        $response->assertStatus(200)->assertSee([
            'status',
            'message'
        ]);
    }

    public function testUpdateQP()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();
        Event::dispatch();
        $response = $this->json('PUT','/quartal-premium/update', [
            "quartal_premium_id"    => rand(0, 5),
            "title"                 => $faker->word,
        ]);

        $response->assertStatus(200)->assertSuccessful()->assertSee([
            'status',
            'message'
        ]);
    }

    public function testDeleteQP()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('DELETE','/quartal-premium/delete', [
            "id"         => rand(1, 10),
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }
}
