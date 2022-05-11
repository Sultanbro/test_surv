<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgramToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('b_user', function (Blueprint $table) {
            $table->unsignedInteger('program_id')->after('user_type')->nullable();
            $table->foreign('program_id')->references('id')->on('program');
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
            $table->dropColumn('program_id');
        });
    }
}
