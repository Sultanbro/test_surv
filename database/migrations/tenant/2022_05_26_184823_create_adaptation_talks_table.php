<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdaptationTalksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adaptation_talks', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('day');
            $table->integer('user_id');
            $table->string('inter_id')->nullable();
            $table->string('text')->nullable();
            $table->timestamps();
            $table->date('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adaptation_talks');
    }
}
