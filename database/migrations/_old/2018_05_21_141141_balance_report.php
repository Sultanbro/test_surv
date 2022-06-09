<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BalanceReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('balance_daily_report', function (Blueprint $table) {
		    $table->engine = 'InnoDB';
		    $table->integer('user_id');
		    $table->float('d1_balance')->nullable();
		    $table->float('d2_balance')->nullable();
		    $table->float('d3_balance')->nullable();
		    $table->float('d4_balance')->nullable();
		    $table->float('d5_balance')->nullable();
		    $table->float('d6_balance')->nullable();
		    $table->float('d7_balance')->nullable();
		    $table->float('d8_balance')->nullable();
		    $table->float('d9_balance')->nullable();
		    $table->float('d10_balance')->nullable();
		    $table->float('d11_balance')->nullable();
		    $table->float('d12_balance')->nullable();
		    $table->float('d13_balance')->nullable();
		    $table->float('d14_balance')->nullable();
		    $table->float('d15_balance')->nullable();
		    $table->float('d16_balance')->nullable();
		    $table->float('d17_balance')->nullable();
		    $table->float('d18_balance')->nullable();
		    $table->float('d19_balance')->nullable();
		    $table->float('d20_balance')->nullable();
		    $table->float('d21_balance')->nullable();
		    $table->float('d22_balance')->nullable();
		    $table->float('d23_balance')->nullable();
		    $table->float('d24_balance')->nullable();
		    $table->float('d25_balance')->nullable();
		    $table->float('d26_balance')->nullable();
		    $table->float('d27_balance')->nullable();
		    $table->float('d28_balance')->nullable();
		    $table->float('d29_balance')->nullable();
		    $table->float('d30_balance')->nullable();
		    $table->float('d31_balance')->nullable();
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
