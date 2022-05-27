<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiDialingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_dialing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('status')->default(0);
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->string('start_date', 191);
            $table->integer('group_id')->default(0);
            $table->integer('schema_id')->nullable();
            $table->string('call_repeat', 191);
            $table->string('call_repeat_info', 191)->nullable();
            $table->integer('call_dialing_count')->default(0);
            $table->string('call_start_time', 191);
            $table->string('call_end_time', 191);
            $table->integer('daily_limit')->default(0);
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
        Schema::dropIfExists('ai_dialing');
    }
}
