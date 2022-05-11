<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToTarrifs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarrifs', function (Blueprint $table) {
            $table->dropColumn('kzt');
            $table->dropColumn('rub');
            $table->string('autocall_bill_duration')->after('direction');
            $table->string('autocall_cost')->after('direction');
            $table->string('autocall_cost2')->after('direction');
            $table->string('autocall_cost3')->after('direction');
            $table->string('autocall_transfer_cost')->after('direction');
            $table->string('autocall_funct_cost')->after('direction');
            $table->string('autocall_sms_cost')->after('direction');
            $table->string('autocall_integration_cost')->after('direction');
            $table->string('autocall_integration_transfer_cost')->after('direction');
            $table->string('autocall_integration_funct_cost')->after('direction');
            $table->string('autocall_transfer_bill_duration')->after('direction');
            $table->string('autocall_sip_cost')->after('direction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarrifs', function (Blueprint $table) {
            $table->dropColumn('autocall_bill_duration');
            $table->dropColumn('autocall_cost');
            $table->dropColumn('autocall_cost2');
            $table->dropColumn('autocall_cost3');
            $table->dropColumn('autocall_transfer_cost');
            $table->dropColumn('autocall_funct_cost');
            $table->dropColumn('autocall_sms_cost');
            $table->dropColumn('autocall_integration_cost');
            $table->dropColumn('autocall_integration_transfer_cost');
            $table->dropColumn('autocall_integration_funct_cost');
            $table->dropColumn('autocall_transfer_bill_duration');
            $table->dropColumn('autocall_sip_cost');
            $table->decimal('kzt')->after('direction');
            $table->decimal('rub')->after('direction');
        });
    }
}
