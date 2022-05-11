<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToGroupIndicators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_indicators', function (Blueprint $table) {
            $table->unsignedInteger('plan')->default(0);
            $table->string('plan_unit')->after('plan')->default('percent');
            $table->unsignedInteger('ud_ves')->after('plan')->default(0);
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
