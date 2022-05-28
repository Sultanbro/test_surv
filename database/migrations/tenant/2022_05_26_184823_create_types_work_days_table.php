<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesWorkDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types_work_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('halfday')->nullable();
            $table->integer('fullday')->nullable();
            $table->integer('holiday')->nullable();
            $table->integer('absense')->nullable();
            $table->integer('sickday')->nullable();
            $table->integer('fired')->nullable();
            $table->integer('intern')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types_work_days');
    }
}
