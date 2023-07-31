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
        if (!Schema::hasColumn('work_charts', 'floating_dayoffs')) {
            Schema::table('work_charts', function (Blueprint $table) {
                $table->integer('floating_dayoffs')->nullable()->comment('Плавающий выходной');
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
        if (Schema::hasColumn('work_charts', 'floating_dayoffs')) {
            Schema::table('work_charts', function (Blueprint $table) {
                $table->dropColumn('floating_dayoffs');
            });
        }
    }
};
