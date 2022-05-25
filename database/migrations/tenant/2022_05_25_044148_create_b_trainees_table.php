<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_trainees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->timestamp('applied')->nullable();
            $table->timestamps();
            $table->timestamp('requested')->nullable();
            $table->timestamp('fired')->nullable();
            $table->integer('lead_id')->default(0);
            $table->integer('deal_id')->default(0);
            $table->integer('bitrix')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_trainees');
    }
}
