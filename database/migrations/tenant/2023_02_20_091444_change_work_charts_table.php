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
        Schema::table('work_charts', function (Blueprint $table) {
            $table->renameColumn('time_beg', 'start_time');
            $table->renameColumn('time_end', 'end_time');
            $table->dropColumn('day_off');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_charts', function (Blueprint $table) {
            $table->renameColumn('start_time', 'time_beg');
            $table->renameColumn('end_time', 'time_end');
            $table->json('day_off')->comment('выходные дни');
        });
    }
};
