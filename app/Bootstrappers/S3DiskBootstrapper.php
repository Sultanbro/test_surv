<?php

declare(strict_types=1);

namespace App\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;

class S3DiskBootstrapper extends FilesystemTenancyBootstrapper
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function bootstrap(Tenant $tenant)
    {
        config(['filesystems.disks.s3.bucket' => 'tenant'. $tenant->getTenantKey()]);
        parent::bootstrap($tenant);
    }
}