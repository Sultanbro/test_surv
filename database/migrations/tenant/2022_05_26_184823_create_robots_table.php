<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRobotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('name', 191);
            $table->tinyInteger('status')->default(1);
            $table->string('type', 191)->nullable()->default('1');
            $table->integer('entity_id')->default(0);
            $table->text('message')->nullable();
            $table->integer('voice_id')->default(0);
            $table->string('condition', 191);
            $table->integer('dftm')->nullable();
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
        Schema::dropIfExists('robots');
    }
}
