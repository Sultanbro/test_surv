<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->decimal('amount', 10)->nullable();
            $table->text('note')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            $table->integer('paid')->default(0);
            $table->integer('bonus')->default(0);
            $table->integer('award')->default(0);
            $table->string('comment_paid')->nullable();
            $table->string('comment_bonus')->nullable();
            $table->string('comment_award')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
