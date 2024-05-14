<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('calls_per_day')->nullable();
            $table->integer('minutes_per_day')->nullable();
            $table->integer('consent_per_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_plan');
    }
}
