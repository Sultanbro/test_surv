<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->default(0);
            $table->text('text');
            $table->text('variants')->nullable();
            $table->integer('order')->default(0);
            $table->tinyInteger('points')->default(0);
            $table->integer('page')->nullable()->default(0);
            $table->integer('testable_id')->index();
            $table->string('testable_type')->index();
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
        Schema::dropIfExists('test_questions');
    }
}
