<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalCostAndSendForCurrentBalanceAutocallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autocalls', function (Blueprint $table) {
            $table->string('total_cost', 191)->after('call_repeat_info')->nullable();
            $table->tinyInteger('send_remaining_balance')->after('total_cost')->default(0);
            $table->string('group_id', 191)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autocalls', function (Blueprint $table) {
            $table->dropColumn('total_cost');
            $table->dropColumn('send_remaining_balance');
            $table->integer('group_id')->default(0)->change();
        });
    }
}
