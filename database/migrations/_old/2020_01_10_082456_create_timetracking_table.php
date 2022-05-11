<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimetrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetracking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->dateTime('first_enter')->nullable();
            $table->dateTime('last_exit')->nullable();
            $table->dateTime('enter')->nullable();
            $table->dateTime('exit')->nullable();
            $table->bigInteger('total_hours')->default(0);
            $table->tinyinteger('status')->nullable();
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
        Schema::dropIfExists('timetracking');
    }
}
