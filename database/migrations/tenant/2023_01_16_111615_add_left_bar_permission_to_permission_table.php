<?php

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
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            DB::table('permissions')->insert([
                [
                    'name' => 'show_depremirovanie',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'show_faq',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            DB::table('permissions')->whereIn('name', ['show_depremirovanie', 'show_faq'])->delete();
        });
    }
};
