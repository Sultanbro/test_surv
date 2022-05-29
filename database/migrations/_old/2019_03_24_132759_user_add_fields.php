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
        Schema::table('users', function (Blueprint $table) {
            $table->string('BALANCE_NOTIFY')->nullable()->after('email');
            $table->text('DESCRIPTION')->nullable()->after('email');
            $table->string('COMPANY')->nullable()->after('email');
            $table->text('ADDRESS')->nullable()->after('email');
            $table->string('CITY')->nullable()->after('email');
            $table->string('PHONE')->nullable()->after('email');
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
