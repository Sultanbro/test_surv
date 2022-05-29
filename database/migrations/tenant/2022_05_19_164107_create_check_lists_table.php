<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('title');
            $table->integer('auth_id');
            $table->string('auth_name');
            $table->string('auth_last_name');
            $table->string('active_check_text');
            $table->integer('count_view');
            $table->integer('item_type')->default(1);
            $table->integer('item_id')->nullable();
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
