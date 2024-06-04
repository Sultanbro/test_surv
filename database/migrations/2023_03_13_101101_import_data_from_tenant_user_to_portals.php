<?php

use App\Enums\Payments\CurrencyEnum;
use App\Models\Portal\Portal;
use App\Models\TenantUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tenantUsers = DB::connection('mysql')->table('tenant_pivot')->get();
        $portalOwners = [];
        foreach ($tenantUsers as $tenantUser)
        {
            $portalOwners[] = [
                'owner_id'  => $tenantUser->user_id,
                'tenant_id' => $tenantUser->tenant_id,
                'currency'  => CurrencyEnum::KZT,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::connection('mysql')->table('portals')->insert($portalOwners);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::connection('mysql')->table('portals')->delete();
    }
};
