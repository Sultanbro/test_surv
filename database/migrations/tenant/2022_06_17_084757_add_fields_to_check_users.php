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
        Schema::table('check_users', function (Blueprint $table) {
            $table->text('middleware_count')->nullable();
            $table->text('middleware_auth')->nullable();
            $table->text('middleware_next_time')->nullable();
            $table->string('work_start')->nullable();
            $table->string('work_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_users', function (Blueprint $table) {
            //
        });
    }
};
