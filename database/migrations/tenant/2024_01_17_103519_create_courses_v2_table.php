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
            $table->string('tenant_id');
            $table->tinyInteger('type');
            $table->integer('order');
            $table->date('start');
            $table->date('stop');
            $table->string('name');
            $table->string('short');
            $table->text('desc');
            $table->string('icon');
            $table->string('background');
            $table->unsignedBigInteger('curator_id')->nullable();
            $table->unsignedBigInteger('curator_position_id')->nullable();
            $table->unsignedBigInteger('curator_group_id')->nullable();
            $table->unsignedBigInteger('award_id');
            $table->integer('bonus');
            $table->tinyInteger('show_as_finished')->default(0);
            $table->tinyInteger('mix_questions')->default(0);
            $table->tinyInteger('show_answers')->default(0);
            $table->tinyInteger('for_sale')->default(0);
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
