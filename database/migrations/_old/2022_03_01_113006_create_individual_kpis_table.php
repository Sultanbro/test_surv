<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndividualKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_individual', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('kpi_80_99');
            $table->integer('kpi_100');
            $table->integer('nijn_porok');
            $table->integer('verh_porok');
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
        Schema::dropIfExists('individual_kpis');
    }
}
