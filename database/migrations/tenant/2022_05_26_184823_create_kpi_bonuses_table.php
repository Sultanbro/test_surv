<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('sum')->default(0);
            $table->integer('group_id');
            $table->integer('activity_id');
            $table->string('unit', 20);
            $table->integer('quantity');
            $table->tinyInteger('daypart');
            $table->text('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpi_bonuses');
    }
}
