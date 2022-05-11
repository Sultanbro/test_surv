<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhonesToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_user', function (Blueprint $table) {
            $table->char('phone_1')->after('PHONE')->nullable();
            $table->char('phone_2')->after('phone_1')->nullable();
            $table->char('phone_3')->after('phone_2')->nullable();
            $table->char('phone_4')->after('phone_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('b_user', function (Blueprint $table) {
            $table->dropColumn('phone_1');
            $table->dropColumn('phone_2');
            $table->dropColumn('phone_3');
            $table->dropColumn('phone_4');
        });
    }
}
