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
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('article_id')->on('articles')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('user_id')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_votes');
    }
};
