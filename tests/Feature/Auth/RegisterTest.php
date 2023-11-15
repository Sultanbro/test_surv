<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_register()
    {
        $data = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'phone' => '12345678901',
            'currency' => 'usd',
        ];

        $response = $this->json('post', '/register', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'link'
        ]);

        $tenantId = tenant('id');

        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'email' => 'john.doe@example.com',
        ]);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenantId
        ], 'mysql');
        $this->assertDatabaseHas('portals', [
            'tenant_id' => $tenantId
        ], 'mysql');
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'email' => 'john.doe@example.com',
        ], 'mysql');
    }
}
