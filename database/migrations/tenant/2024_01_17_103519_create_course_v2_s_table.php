<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses_v2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('central_course_id');
            $table->tinyInteger('type');
            $table->integer('order');
            $table->date('start');
            $table->date('stop');
            $table->string('name');
            $table->string('short');
            $table->text('desc');
            $table->string('icon');
            $table->string('background');
            $table->unsignedBigInteger('curator_id');
            $table->unsignedBigInteger('curator_position_id');
            $table->unsignedBigInteger('curator_group_id');
            $table->unsignedBigInteger('award_id');
            $table->integer('bonus');
            $table->tinyInteger('show_as_finished');
            $table->tinyInteger('mix_questions');
            $table->tinyInteger('show_answers');
            $table->integer('attempts');
            $table->integer('passing_score');
            $table->unsignedBigInteger('author_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses_v2');
    }
};
