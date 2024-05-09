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
        Schema::table('award_user', function (Blueprint $table) {
            $table->string('format')->nullable();

        });
        Schema::table('award_course', function (Blueprint $table) {
            $table->string('format')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('award_user', function (Blueprint $table) {
            $table->dropColumn('format');
        });
        Schema::table('award_course', function (Blueprint $table) {
            $table->dropColumn('format');
        });
    }
};
