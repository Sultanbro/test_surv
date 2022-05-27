<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsSettingsIndividuallyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics_settings_individually', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->integer('group_id');
            $table->integer('user_id');
            $table->date('date');
            $table->text('data');
            $table->timestamps();
            $table->integer('employee_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analytics_settings_individually');
    }
}
