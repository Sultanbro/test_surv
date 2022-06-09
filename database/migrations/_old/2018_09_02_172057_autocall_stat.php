<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AutocallStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autocall_stat', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('autocall_id');
            $table->string('number', 191);
            $table->integer('statistic_id');
            $table->integer('cdr_id')->default(0);
            $table->integer('ivr_dialer_id')->default(0);
            $table->timestamps();

            $table->index('cdr_id');
            $table->index('ivr_dialer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autocall_stat');
    }
}
