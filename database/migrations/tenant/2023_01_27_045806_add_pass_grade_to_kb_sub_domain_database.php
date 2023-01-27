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
        if (!Schema::hasTable('kb'))
        {
            Schema::table('kb', function (Blueprint $table) {
                $table->tinyInteger('pass_grade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kb', function (Blueprint $table) {
            $table->dropColumn('pass_grade');
        });
    }
};
