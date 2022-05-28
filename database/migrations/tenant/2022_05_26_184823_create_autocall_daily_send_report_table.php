<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutocallDailySendReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autocall_daily_send_report', function (Blueprint $table) {
            $table->integer('autocall_id');
            $table->integer('count');
            $table->date('day');

            $table->unique(['autocall_id', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autocall_daily_send_report');
    }
}
