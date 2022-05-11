<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AutocallsSendToday extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('autocall_daily_send_report', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->integer('autocall_id');
			$table->integer('count'); //Количество отправленных звонков
			$table->date('day');
			$table->unique(['autocall_id', 'day']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('autocall_daily_send_report');
	}
}
