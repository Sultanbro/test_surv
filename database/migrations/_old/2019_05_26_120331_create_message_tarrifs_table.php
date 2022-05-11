<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTarrifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_tarrifs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('prefix');
            $table->string('direction');
            $table->string('message_cost');
            $table->string('message_integration_cost');
            $table->string('message_smpp_cost');
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
        Schema::dropIfExists('message_tarrifs');
    }
}
