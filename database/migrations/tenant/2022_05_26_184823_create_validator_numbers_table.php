<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidatorNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validator_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id')->index();
            $table->string('name', 191)->nullable();
            $table->string('number', 100)->index();
            $table->string('cause', 100)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->integer('bilsec')->default(0);
            $table->tinyInteger('is_alive')->default(0)->index();
            $table->timestamps();
            $table->string('cell', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('validator_numbers');
    }
}
