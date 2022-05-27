<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointsToCourseResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('course_id');
            $table->tinyInteger('status')->default(0);
            $table->integer('admin');
            $table->tinyInteger('progress')->default(0);
            $table->timestamp();
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
