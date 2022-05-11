<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentFieldsToPartnerInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_info', function (Blueprint $table) {
            $table->string('bin')->nullable()->after('urname');
            $table->string('fiofiz')->nullable();
            $table->string('iin')->nullable();
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
            $table->dropColumn('bin');
            $table->dropColumn('fiofiz');
            $table->dropColumn('iin');
        });
    }
}
