<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalyticStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytic_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->date('date');
            $table->integer('row_id');
            $table->integer('column_id');
            $table->string('value')->nullable();
            $table->string('show_value')->nullable();
            $table->string('type')->default('initial');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analytic_stats');
    }
}
