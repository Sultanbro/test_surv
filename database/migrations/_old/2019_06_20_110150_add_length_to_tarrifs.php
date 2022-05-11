<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLengthToTarrifs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarrifs', function (Blueprint $table) {
            $table->integer('pre')->after('id')->default(44);
            $table->tinyInteger('length')->after('prefix')->default(11);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarrifs', function (Blueprint $table) {
            $table->dropColumn('pre');
            $table->dropColumn('length');
        });
    }
}
