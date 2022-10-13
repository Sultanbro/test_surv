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
        Schema::table('user_descriptions', function (Blueprint $table) {
            $table->boolean('view_other_awards')->default(0)->comment('Если стоит 1 сотрудник может смотреть награды других');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_descriptions', function (Blueprint $table) {
            $table->dropColumn('view_other_awards');
        });
    }
};
