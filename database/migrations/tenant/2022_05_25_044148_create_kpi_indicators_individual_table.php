<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiIndicatorsIndividualTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_indicators_individual', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id');
            $table->string('source', 20)->default('activity');
            $table->string('cell', 4)->nullable();
            $table->integer('group_id')->nullable()->default(0);
            $table->integer('activity_id');
            $table->double('plan');
            $table->integer('ud_ves');
            $table->string('plan_unit');
            $table->string('unit', 10);
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
        Schema::dropIfExists('kpi_indicators_individual');
    }
}
