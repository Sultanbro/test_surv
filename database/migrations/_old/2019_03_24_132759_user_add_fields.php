<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_user', function (Blueprint $table) {
            $table->string('BALANCE_NOTIFY')->nullable()->after('EMAIL');
            $table->text('DESCRIPTION')->nullable()->after('EMAIL');
            $table->string('COMPANY')->nullable()->after('EMAIL');
            $table->text('ADDRESS')->nullable()->after('EMAIL');
            $table->string('CITY')->nullable()->after('EMAIL');
            $table->string('PHONE')->nullable()->after('EMAIL');
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
