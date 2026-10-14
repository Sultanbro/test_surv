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
        $existCentralEmails = DB::connection('mysql')
            ->table('users')
            ->select(['email'])
            ->get()
            ->pluck('email')
            ->toArray();

        $tenantOnlyUsers = DB::table('users')
            ->select([
                'email',
                'phone',
                'name',
                'last_name',
                'password',
                'birthday',
                'working_city',
                'currency',
            ])
            ->whereNotIn('email', $existCentralEmails)
            ->get()
            ->toArray();

        $newCentralUsers = [];

        foreach ($tenantOnlyUsers as $tenantUser) {
            $newCentralUsers[] = [
                'email' => $tenantUser->email,
                'phone' => $tenantUser->phone ?? '77000000000',
                'name' => $tenantUser->name ?? 'Noname',
                'last_name' => $tenantUser->last_name ?? 'Nolastname',
                'password' => $tenantUser->password,
                'birthday' => $tenantUser->birthday,
                'city' => $tenantUser->working_city,
                'currency' => $tenantUser->currency,
            ];
        }

        DB::connection('mysql')
            ->table('users')
            ->insert($newCentralUsers);
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
