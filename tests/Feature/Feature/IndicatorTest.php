<?php

namespace Tests\Feature\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class IndicatorTest extends TestCase
{
    /**
     * Тест на список показателей (игнорируем middleware в тесте).
     *
     * @return void
     */
    public function testIndicatorsList(): void
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware();

        $response = $this->get('/indicators');
        $response->assertStatus(200)->assertSee([
            'id'            => '',
            'name'          => '',
            'group_id'      => '',
            'daily_plan'    => '',
            'plan_unit'     => '',
            'unit'          => '',
            'plan_type'     => '',
            'type'          => '',
            'weekdays'      => '',
            'ud_ves'        => '',
            'created_at'    => '',
            'updated_at'    => ''
        ]);
    }

    /**
     * Получаем показатель по id
     * @return void
     */
    public function testIndicatorByOne(): void
    {
        $this->withoutMiddleware();

        $response = $this->get('/indicators/10');
        $response->assertStatus(200)->assertSee([
            'id'            => '',
            'name'          => '',
            'group_id'      => '',
            'daily_plan'    => '',
            'plan_unit'     => '',
            'unit'          => '',
            'plan_type'     => '',
            'type'          => '',
            'weekdays'      => '',
            'ud_ves'        => '',
            'created_at'    => '',
            'updated_at'    => ''
        ]);
    }
}
