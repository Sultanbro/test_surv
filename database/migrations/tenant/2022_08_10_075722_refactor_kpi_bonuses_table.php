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
        Schema::table('kpi_bonuses', function (Blueprint $table) {
            $table->integer('targetable_id');
            $table->string('targetable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_bonuses', function (Blueprint $table) {
            $table->dropColumn('targetable_id');
            $table->dropColumn('targetable_type');
        });
    }
};
