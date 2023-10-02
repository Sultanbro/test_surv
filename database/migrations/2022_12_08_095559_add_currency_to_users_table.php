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
        if (!Schema::connection('mysql')->hasColumn('users', 'currency')) {
            Schema::connection('mysql')->table('users', function (Blueprint $table) {
                $table->string('currency', 3)->default('kzt');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('users', function (Blueprint $table) {
            //
        });
    }
};
