<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AwardTypeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function getAwardTypeTest()
    {
        $response = $this->getJson('/award-types/get');
        dd(
            'gere'
        );
        $response->assertJsonStructure([
            'status', Response::HTTP_OK,
            'message' => 'success',
            'data' => [
               '*' => [
                   'id',
                   'name',
                   'description',
                   'created_at',
                   'updated_at'
               ]
            ]
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }
}
