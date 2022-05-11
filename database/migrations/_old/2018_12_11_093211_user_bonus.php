<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_user', function (Blueprint $table) {
            $table->float('bonus')->after('email')->default(0);
        });

        Schema::table('autocalls', function (Blueprint $table) {
                $table->integer('use_bonus')->after('sms_message')->default(0);
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
