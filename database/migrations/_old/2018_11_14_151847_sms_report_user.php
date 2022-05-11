<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmsReportUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_gate_daily_report', function (Blueprint $table) {
            $table->integer('user_id')->after('gate')->default(0);
            $table->dropUnique(['type', 'gate', 'day']);
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
        //
    }
}
