<?php

namespace Tests\Tenant;

class DefaultTestTenantDto
{
    public string $id;
    public string $db;
    public string $name;
    public string $domain;

    public function __construct()
    {
        $this->id = config('tenancy.testing.id');
        $this->db = config('tenancy.testing.db');
        $this->name = config('tenancy.testing.name');
        $this->domain = config('tenancy.testing.domain');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenancy_db_name' => $this->db,
            'name' => $this->name,
            'domain' => $this->domain
        ];
    }
}