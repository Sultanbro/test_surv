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
        if (!Schema::connection('mysql')->hasTable('coordinates')) {
            Schema::connection('mysql')->create('coordinates', function (Blueprint $table) {
                $table->id();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->double('geo_lat', 10, 8);
                $table->double('geo_lon', 11, 8);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection('mysql')->hasTable('coordinates'))
        {
            Schema::dropIfExists('coordinates');
        }
    }
};
