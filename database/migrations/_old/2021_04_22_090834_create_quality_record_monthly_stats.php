<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualityRecordMonthlyStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_record_monthly_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('total');
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
