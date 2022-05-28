<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsGateDailyReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_gate_daily_report', function (Blueprint $table) {
            $table->string('type', 191)->nullable();
            $table->string('gate', 100);
            $table->integer('user_id')->default(0);
            $table->integer('count');
            $table->date('day');

            $table->unique(['type', 'gate', 'user_id', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_gate_daily_report');
    }
}
