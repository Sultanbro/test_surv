<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityRecordMonthlyStatsTable extends Migration
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
            $table->integer('group_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quality_record_monthly_stats');
    }
}
