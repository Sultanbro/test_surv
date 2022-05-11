<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('activity_1')->nullable();
            $table->string('activity_2')->nullable();
            $table->string('activity_3')->nullable();
            $table->decimal('kpi_80_99', 10,2)->default(0);
            $table->decimal('kpi_100', 10,2)->default(0);
            $table->integer('nijn_porok')->default(0);
            $table->integer('verh_porok')->default(0);
            $table->integer('ud_ves_1')->default(0);
            $table->integer('ud_ves_2')->default(0);
            $table->integer('ud_ves_3')->default(0);
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('kpi');
    }
}
