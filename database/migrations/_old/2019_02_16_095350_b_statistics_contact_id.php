<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BStatisticsContactId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_statistics', function (Blueprint $table) {
            //$table->integer('id_contact')->after('id_user')->default(0);
            //$table->string('id_session', 191)->nullable();
            //$table->index('id_contact');
            //$table->index('id_session');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
