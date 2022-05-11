<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ValidatorNumberBilsec extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('validator_numbers', function (Blueprint $table) {
			$table->integer('bilsec')->after('session_id')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('validator_numbers', function($table) {
			$table->dropColumn('bilsec');
		});
	}
}
