<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AutocallsStatisticsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_statistics', function (Blueprint $table) {
            $table->smallInteger('is_laravel')->default(0);
            $table->smallInteger('has_sms')->default(0);
            $table->smallInteger('call_attempt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('b_statistics', function (Blueprint $table) {
            $table->dropColumn('is_laravel');
            $table->dropColumn('has_sms');
            $table->dropColumn('call_attempt');
        });
    }
}
