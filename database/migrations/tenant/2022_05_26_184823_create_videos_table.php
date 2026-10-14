<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('text')->nullable();
            $table->string('poster')->nullable();
            $table->string('domain')->default('local')->nullable();
            $table->unsignedInteger('group_id')->default(0);
            $table->text('links');
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('playlist_id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('order')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
