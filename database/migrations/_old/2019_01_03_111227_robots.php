<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Robots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robots', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('name', 191);
            $table->integer('type')->default(1); // 1 - автозвонки, 2 - смс рассылки
            $table->integer('entity_id')->default(0);
            $table->string('action', 191);
            $table->text('message');
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
        //
    }
}
