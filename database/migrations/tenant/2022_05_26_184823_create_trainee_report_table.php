<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_report', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('group_id');
            $table->integer('day_1')->default(0);
            $table->integer('day_2')->default(0);
            $table->integer('day_3')->default(0);
            $table->integer('day_4')->default(0);
            $table->integer('day_5')->default(0);
            $table->integer('day_6')->default(0);
            $table->integer('day_7')->default(0);
            $table->text('data')->nullable();
            $table->timestamps();
            $table->integer('leads')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainee_report');
    }
}
