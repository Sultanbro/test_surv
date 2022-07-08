<?php

namespace Tests\Unit;

use Tests\TestCase;

class CheckTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertOk();
       // $this->assertEquals(200, $response->status());
    }

    public function testAuthorize()
    {
        $this->post('/login', [
            'email' => 'ali.akpanov@yaxndex.ru',
            'password' => 'jJyvhXYF'
        ]);

        $response = $this->get('/kb');
        $response->assertUnauthorized();
    }
}
