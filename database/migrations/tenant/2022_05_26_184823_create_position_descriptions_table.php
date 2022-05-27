<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('position_id');
            $table->text('require')->nullable();
            $table->text('actions')->nullable();
            $table->text('time')->nullable();
            $table->text('salary')->nullable();
            $table->text('knowledge')->nullable();
            $table->text('next_step')->nullable();
            $table->tinyInteger('show')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_descriptions');
    }
}
