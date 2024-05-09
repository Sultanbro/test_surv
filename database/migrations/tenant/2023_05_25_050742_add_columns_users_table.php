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
        if (!Schema::hasColumn('users', 'first_work_day')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('first_work_day')->nullable();
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
        if (Schema::hasColumn('users', 'first_work_day')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('first_work_day');
            });
        }
    }
};
