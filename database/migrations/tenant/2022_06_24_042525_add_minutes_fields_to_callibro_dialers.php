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
        Schema::table('callibro_dialers', function (Blueprint $table) {
            $table->integer('talk_hours')->default(0);
            $table->integer('talk_minutes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('callibro_dialers', function (Blueprint $table) {
            //
        });
    }
};
