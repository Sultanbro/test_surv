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
        Schema::table('tariff_payment', function (Blueprint $table) {
            $table->string('payment_id')->after('auto_payment')->nullable();
            $table->string('service_for_payment')->after('payment_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tariff_payment', function (Blueprint $table) {
            $table->dropColumn('payment_id');
            $table->dropColumn('service_for_payment ');
        });
    }
};
