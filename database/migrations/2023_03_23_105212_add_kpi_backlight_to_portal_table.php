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
        Schema::connection('mysql')->table('portals', function (Blueprint $table) {
            $table->json('kpi_backlight')
                ->comment('Цвет ячеек kpi')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('portals', function (Blueprint $table) {
            $table->dropColumn('kpi_backlight');
        });
    }
};
