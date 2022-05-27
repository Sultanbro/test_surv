<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->index();
            $table->dateTime('exam_date')->nullable();
            $table->integer('book_id')->nullable();
            $table->boolean('success')->nullable();
            $table->string('link')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->boolean('bonus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
