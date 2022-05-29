<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBUserDeletePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_delete_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->timestamp('delete_time')->useCurrentOnUpdate()->useCurrent();
            $table->tinyInteger('executed')->default(0);
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
        Schema::dropIfExists('users_delete_plans');
    }
}
