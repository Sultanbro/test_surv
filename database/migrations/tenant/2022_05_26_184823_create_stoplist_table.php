<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoplistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stoplist', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('number', 100);
            $table->timestamp('date')->useCurrentOnUpdate()->useCurrent();
            $table->string('type', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stoplist');
    }
}
