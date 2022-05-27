<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bp_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_cat_id')->nullable();
            $table->string('name');
            $table->integer('group_id');
            $table->integer('queue_number');
            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bp_category');
    }
}
