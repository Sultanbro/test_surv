<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SipReport extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sip_daily_report', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->integer('user_id');
			$table->integer('d1_seconds')->nullable();
			$table->integer('d2_seconds')->nullable();
			$table->integer('d3_seconds')->nullable();
			$table->integer('d4_seconds')->nullable();
			$table->integer('d5_seconds')->nullable();
			$table->integer('d6_seconds')->nullable();
			$table->integer('d7_seconds')->nullable();
			$table->integer('d8_seconds')->nullable();
			$table->integer('d9_seconds')->nullable();
			$table->integer('d10_seconds')->nullable();
			$table->integer('d11_seconds')->nullable();
			$table->integer('d12_seconds')->nullable();
			$table->integer('d13_seconds')->nullable();
			$table->integer('d14_seconds')->nullable();
			$table->integer('d15_seconds')->nullable();
			$table->integer('d16_seconds')->nullable();
			$table->integer('d17_seconds')->nullable();
			$table->integer('d18_seconds')->nullable();
			$table->integer('d19_seconds')->nullable();
			$table->integer('d20_seconds')->nullable();
			$table->integer('d21_seconds')->nullable();
			$table->integer('d22_seconds')->nullable();
			$table->integer('d23_seconds')->nullable();
			$table->integer('d24_seconds')->nullable();
			$table->integer('d25_seconds')->nullable();
			$table->integer('d26_seconds')->nullable();
			$table->integer('d27_seconds')->nullable();
			$table->integer('d28_seconds')->nullable();
			$table->integer('d29_seconds')->nullable();
			$table->integer('d30_seconds')->nullable();
			$table->integer('d31_seconds')->nullable();
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
		Schema::dropIfExists('sip_daily_report');
	}
}
