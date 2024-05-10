<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_fines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('fine_id');
            $table->dateTime('day');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->integer('status')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_fines');
    }
}
