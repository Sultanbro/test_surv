<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruiterStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruiter_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('profile')->default(0);
            $table->integer('dials')->nullable()->default(0);
            $table->integer('calls')->default(0);
            $table->integer('minutes')->default(0);
            $table->integer('converts')->default(0);
            $table->integer('leads')->default(0);
            $table->tinyInteger('hour');
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recruiter_stats');
    }
}
