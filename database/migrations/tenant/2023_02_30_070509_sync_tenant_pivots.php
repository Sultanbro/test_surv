<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tenantUserEmails = DB::table('users')
            ->select(['email'])
            ->get()
            ->pluck('email')
            ->toArray();

        $centralUserIds = DB::connection('mysql')
            ->table('users')
            ->select(['id'])
            ->whereIn('email', $tenantUserEmails)
            ->get()
            ->pluck('id')
            ->toArray();
        
        $tenantId = tenant('id');

        $tenantPivotUserIds = DB::connection('mysql')
            ->table('tenant_pivot')
            ->select(['user_id'])
            ->where('tenant_id', $tenantId)
            ->whereIn('user_id', $centralUserIds)
            ->get()
            ->pluck('user_id')
            ->toArray();

        $newTenantPivots = [];

        foreach ($centralUserIds as $centralUserId) {
            if (in_array($centralUserId, $tenantPivotUserIds )) continue;

            $newTenantPivots[] = [
                'tenant_id' => $tenantId,
                'user_id' => $centralUserId,
                'owner' => 0,
            ];
        }

        DB::connection('mysql')
            ->table('tenant_pivot')
            ->insert($newTenantPivots);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
