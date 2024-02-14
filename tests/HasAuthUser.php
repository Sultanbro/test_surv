<?php

namespace Tests;

use App\User;

trait HasAuthUser
{
    private function authenticate(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
}
