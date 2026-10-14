<?php

namespace Tests\Tenant;

use App\User;

trait HasAuthUser
{
    protected function givenUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }
}
