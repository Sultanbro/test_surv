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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            //С какой таблицы имеет смязь
            $table->string('reference_table');
            //С каким записем из таблицы имеет связь
            $table->integer('reference_id')->unsigned();
            //Кто делал изменение
            $table->integer('actor_id')->unsigned();
            //Данные которые изменились
            $table->jsonb('payload');
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
        Schema::dropIfExists('histories');
    }
};
