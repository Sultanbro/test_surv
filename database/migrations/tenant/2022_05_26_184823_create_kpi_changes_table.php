<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kpi_id');
            $table->unsignedInteger('kpi_80_99');
            $table->unsignedInteger('nijn_porok');
            $table->unsignedInteger('verh_porok');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->timestamps();
            $table->unsignedInteger('kpi_100');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_changes');
    }
}
