<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutocallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autocalls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('status')->default(0);
            $table->string('name', 191);
            $table->tinyInteger('is_integration')->default(0);
            $table->string('direction_country_prefix', 191)->nullable();
            $table->text('description')->nullable();
            $table->integer('voice_id')->default(0);
            $table->text('message')->nullable();
            $table->string('start_date', 191)->nullable();
            $table->string('start_time', 191)->nullable();
            $table->tinyInteger('is_saturday_send')->default(0);
            $table->tinyInteger('is_sunday_send')->default(0);
            $table->string('group_id', 191)->default('0');
            $table->string('call_repeat', 191)->nullable();
            $table->text('call_repeat_info')->nullable();
            $table->string('total_cost', 191)->nullable();
            $table->tinyInteger('send_remaining_balance')->default(0);
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
            $table->text('sms_message')->nullable();
            $table->integer('use_bonus')->default(0);
            $table->tinyInteger('early')->default(0);
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
    }
}
