<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_downloads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('ud_lich', 191)->nullable();
            $table->string('dog_okaz_usl', 191)->nullable();
            $table->string('sohr_kom_tainy', 191)->nullable();
            $table->string('dog_o_nekonk', 191)->nullable();
            $table->string('trud_dog', 191)->nullable();
            $table->string('archive', 191)->nullable();
            $table->timestamps();
            $table->string('resignation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_downloads');
    }
}
