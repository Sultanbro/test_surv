<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualityParamValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_param_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('param_id');
            $table->integer('record_id');
            $table->tinyInteger('value');
         //   $table->foreign('record_id')->references('id')->on('quality_records');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quality_param_values');
    }
}
