<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidatorFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validator_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('title', 191);
            $table->string('file', 191);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('db_checked')->default(0);
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
        Schema::dropIfExists('validator_files');
    }
}
