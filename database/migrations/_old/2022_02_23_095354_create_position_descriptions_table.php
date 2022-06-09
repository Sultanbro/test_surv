<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->text('require');
            $table->text('actions');
            $table->text('time');
            $table->text('salary');
            $table->text('knowledge');
            $table->text('next_step');
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
