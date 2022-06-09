<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypesWorkDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types_work_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('halfday')->nullable();//Пол дня
            $table->integer('fullday')->nullable();//Полный день
            $table->integer('holiday')->nullable();//Выходной
            $table->integer('absense')->nullable();//Прогул
            $table->integer('sickday')->nullable();//Больничный
            $table->integer('fired')->nullable();//Уволенный
            $table->integer('intern')->nullable();//Стажер
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
        Schema::dropIfExists('types_work_days');
    }
}
