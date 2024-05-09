<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kb', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable()->index('parent_id');
            $table->string('title');
            $table->integer('user_id');
            $table->integer('editor_id');
            $table->text('text');
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('hash');
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kb');
    }
}
