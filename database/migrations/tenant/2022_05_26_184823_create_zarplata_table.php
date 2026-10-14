<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZarplataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zarplata', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique('zarplata_pk');
            $table->integer('zarplata')->nullable();
            $table->timestamps();
            $table->string('card_number', 16)->nullable();
            $table->string('jysan', 30)->nullable();
            $table->string('kaspi', 30)->nullable();
            $table->string('card_jysan')->nullable();
            $table->string('card_kaspi')->nullable();
            $table->string('kaspi_cardholder')->nullable()->default('');
            $table->string('jysan_cardholder')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zarplata');
    }
}
