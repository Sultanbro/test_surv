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
        Schema::create('work_charts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Название графика');
            $table->string('time_beg')->comment('рабочий график - начало');
            $table->string('time_end')->comment('рабочий график - конец');
            $table->json('day_off')->comment('выходные дни');
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
        Schema::dropIfExists('work_charts');
    }
};
