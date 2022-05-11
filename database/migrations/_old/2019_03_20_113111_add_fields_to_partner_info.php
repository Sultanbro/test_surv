<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPartnerInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_info', function (Blueprint $table) {
            $table->string('bankname')->nullable();
            $table->string('iik')->nullable();
            $table->string('urname')->nullable();
            $table->string('banknamefiz')->nullable();
            $table->string('iikfiz')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_info', function (Blueprint $table) {
            $table->dropColumn('bankname');
            $table->dropColumn('iik');
            $table->dropColumn('urname');
            $table->dropColumn('banknamefiz');
            $table->dropColumn('iikfiz');
        });
    }
}
