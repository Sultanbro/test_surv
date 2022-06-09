<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('UF_API_KEY')->nullable();
            $table->text('UF_SIP_ACC')->nullable();
            $table->text('UF_BALANCE')->nullable();
            $table->text('UF_SMPP')->nullable();
            $table->text('UF_ADMIN')->nullable();
            $table->text('UF_LOGO')->nullable();
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
