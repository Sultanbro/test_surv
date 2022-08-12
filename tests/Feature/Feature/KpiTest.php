<?php

namespace Tests\Feature\Feature;

use App\Events\Event;
use App\Models\Kpi\Kpi;
use App\User;
use Illuminate\Auth\Authenticatable;
use Tests\TestCase;
use Faker\Factory as Faker;

class KpiTest extends TestCase
{
    /**
     * Получить Kpi.
     *
     *
     * @return void
     */
    public function testGetKpi(): void
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('GET','/kpi/get', [
            'id' => 4
        ]);

        $response->assertStatus(200)->assertSee([
            'kpis'       => json_encode([
            ]),
            'activities' => json_encode([
            ])
        ]);
    }

    public function testSaveKpi()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();
        $kpi = Kpi::factory()->make()->first();

        $response = $this->json('POST','/kpi/save', [
            "targetableId"   => $kpi->targetable_id,
            "targetableType" => array_rand(['App\User', 'App\ProfileGroup', 'App\Position']),
            "completed_80"   => $kpi->completed_80,
            "completed_100"  => $kpi->completed_100,
            "lower_limit"    => $kpi->lower_limit,
            "upper_limit"    => $kpi->upper_limit,
            "colors"         => [
                'red'   => $faker->randomNumber(),
                'green' => $faker->randomNumber()
            ]
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }

    public function testUpdateKpi()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();
        $faker = Faker::create();
        Event::dispatch();
        $response = $this->json('PUT','/kpi/update', [
            "kpi_id"         => rand(0, 5),
            "targetableId"   => $faker->randomDigit,
            "targetableType" => array_rand(['App\User', 'App\ProfileGroup', 'App\Position']),
            "completed_80"   => $faker->numberBetween(0,100),
            "completed_100"  => $faker->numberBetween(0,100),
            "lower_limit"    => $faker->numberBetween(0,100),
            "upper_limit"    => $faker->numberBetween(0,100),
            "colors"         => [
                'red'   => $faker->randomNumber(),
                'green' => $faker->randomNumber()
            ]
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }

    public function testDeleteKpi()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->json('DELETE','/kpi/delete', [
            "kpi_id"         => rand(1, 10),
        ]);

        $response->assertStatus(200)->assertSuccessful();
    }
}
