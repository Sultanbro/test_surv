<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmsGateDaylyReport extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sms_gate_daily_report', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->string('gate', 100);
			$table->integer('count'); //Количество отправленных смс
			$table->date('day');
			$table->unique(['gate', 'day']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('sms_gate_daily_report');
	}
}
