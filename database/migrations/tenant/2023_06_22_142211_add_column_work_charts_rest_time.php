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
        if (!Schema::hasColumn('work_charts', 'rest_time')) {
            Schema::table('work_charts', function (Blueprint $table) {
                $table->integer('rest_time')->nullable()->default(0)->comment("Время отдыха ежедневно");
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
        if (Schema::hasColumn('work_charts', 'rest_time')) {
            Schema::table('work_charts', function (Blueprint $table) {
                $table->dropColumn('rest_time');
            });
        }
    }
};
