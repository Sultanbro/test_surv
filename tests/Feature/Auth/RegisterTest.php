<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_visit_register_page()
    {
        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSeeText('Включайся в работу и наслаждайся');
    }

    public function test_tenant_can_register()
    {
        // spy
        $data = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'phone' => '12345678901',
            'currency' => 'usd',
        ];

        //request
        $response = $this->json('post', '/register', $data);
        $tenantId = tenant('id');

        // assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'link'
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'email' => 'john.doe@example.com',
        ]); // tenants database
        $this->assertDatabaseHas('tenants', [
            'id' => $tenantId
        ], 'mysql');
        $this->assertDatabaseHas('portals', [
            'tenant_id' => $tenantId
        ], 'mysql');
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'email' => 'john.doe@example.com',
        ], 'mysql'); // central database
    }
}
