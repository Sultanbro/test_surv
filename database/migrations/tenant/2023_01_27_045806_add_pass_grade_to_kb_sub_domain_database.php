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
        if (!Schema::hasColumn('kb', 'pass_grade'))
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
        if (Schema::hasColumn('kb', 'pass_grade'))
        {
            Schema::table('kb', function (Blueprint $table) {
                $table->dropColumn('pass_grade');
            });
        }
    }
};
