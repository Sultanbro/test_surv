<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceDailyReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_daily_report', function (Blueprint $table) {
            $table->integer('user_id');
            $table->double('d1_balance')->nullable();
            $table->double('d2_balance')->nullable();
            $table->double('d3_balance')->nullable();
            $table->double('d4_balance')->nullable();
            $table->double('d5_balance')->nullable();
            $table->double('d6_balance')->nullable();
            $table->double('d7_balance')->nullable();
            $table->double('d8_balance')->nullable();
            $table->double('d9_balance')->nullable();
            $table->double('d10_balance')->nullable();
            $table->double('d11_balance')->nullable();
            $table->double('d12_balance')->nullable();
            $table->double('d13_balance')->nullable();
            $table->double('d14_balance')->nullable();
            $table->double('d15_balance')->nullable();
            $table->double('d16_balance')->nullable();
            $table->double('d17_balance')->nullable();
            $table->double('d18_balance')->nullable();
            $table->double('d19_balance')->nullable();
            $table->double('d20_balance')->nullable();
            $table->double('d21_balance')->nullable();
            $table->double('d22_balance')->nullable();
            $table->double('d23_balance')->nullable();
            $table->double('d24_balance')->nullable();
            $table->double('d25_balance')->nullable();
            $table->double('d26_balance')->nullable();
            $table->double('d27_balance')->nullable();
            $table->double('d28_balance')->nullable();
            $table->double('d29_balance')->nullable();
            $table->double('d30_balance')->nullable();
            $table->double('d31_balance')->nullable();
            $table->integer('yearmonth');

            $table->unique(['user_id', 'yearmonth']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_daily_report');
    }
}
