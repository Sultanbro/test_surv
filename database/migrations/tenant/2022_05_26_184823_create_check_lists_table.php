<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name');
            $table->string('user_last_name');
            $table->string('title');
            $table->integer('user_id');
            $table->integer('auth_id');
            $table->integer('item_id');
            $table->integer('item_type');
            $table->string('auth_name');
            $table->string('auth_last_name');
            $table->string('active_check_text');
            $table->integer('count_view');
            $table->string('role_check')->nullable();
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
        Schema::dropIfExists('check_lists');
    }
}
