<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Autocalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autocalls', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('status')->default(0);
            $table->string('name', 191);
            $table->text('description');
            $table->integer('voice_id')->default(0);
            $table->string('message', 191);
            $table->string('start_date', 191);
            $table->string('start_time', 191);
            $table->integer('group_id')->default(0);
            $table->string('call_repeat', 191); //daily, weekly, monthly
            $table->string('call_repeat_info', 191)->nullable(); //serialized array [days, time]
            $table->integer('call_dialing_count')->default(0);
            $table->string('call_start_time', 191);
            $table->string('call_end_time', 191);
            $table->integer('daily_limit')->default(0);
            $table->tinyInteger('has_funct')->default(0);
            $table->string('funct_0', 191)->nullable();
            $table->string('funct_1', 191)->nullable();
            $table->string('funct_2', 191)->nullable();
            $table->string('funct_3', 191)->nullable();
            $table->string('funct_4', 191)->nullable();
            $table->string('funct_5', 191)->nullable();
            $table->string('funct_6', 191)->nullable();
            $table->string('funct_7', 191)->nullable();
            $table->string('funct_8', 191)->nullable();
            $table->string('funct_9', 191)->nullable();
            $table->tinyInteger('has_sms')->default(0);
            $table->tinyInteger('sms_multiname')->default(0);
            $table->text('sms_message');
            $table->timestamps();
        });


        Schema::create('voices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('title', 191);
            $table->string('file', 191);
            $table->string('type', 191)->default('wav'); //wav, record, syntez
            $table->integer('duration')->default(0);
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
        Schema::dropIfExists('autocalls');
        Schema::dropIfExists('voices');
    }
}
