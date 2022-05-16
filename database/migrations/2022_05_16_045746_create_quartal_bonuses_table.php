<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuartalBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quartals_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('auth_id');
            $table->integer('quartal');
            $table->integer('sum');
            $table->string('year');
            $table->text('text');
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
        Schema::dropIfExists('quartal_bonuses');
    }
}
