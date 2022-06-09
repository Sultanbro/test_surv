<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ValidatorNumbers extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('validator_numbers', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('file_id');
			$table->string('name', 191)->nullable();
			$table->string('number', 100);
			$table->string('cause', 100)->nullable();
			$table->string('session_id', 100)->nullable();
			$table->tinyInteger('is_alive')->default(0);
			$table->timestamps();

			$table->index('file_id');
			$table->index('number');
			$table->index('is_alive');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('validator_numbers');
	}
}
