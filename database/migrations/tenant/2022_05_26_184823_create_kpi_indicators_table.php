<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('group_id');
            $table->integer('activity_id')->default(0);
            $table->timestamps();
            $table->double('plan')->default(0);
            $table->unsignedInteger('ud_ves')->default(0);
            $table->string('plan_unit')->default('percent');
            $table->string('unit', 10)->default('');
            $table->string('plan_type')->default('sum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_indicators');
    }
}
