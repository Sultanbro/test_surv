<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_segments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('book_id');
            $table->integer('page_start')->default(0);
            $table->integer('page_end')->default(0);
            $table->tinyInteger('pass_grade')->default(1);
            $table->timestamps();

            $table->index('book_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_segments');
    }
};
