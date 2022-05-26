<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('row_id')->index();
            $table->integer('column_id')->index();
            $table->string('value')->nullable();
            $table->string('show_value')->nullable();
            $table->string('class')->nullable();
            $table->string('type')->default('initial');
            $table->timestamps();
            $table->integer('activity_id')->nullable();
            $table->tinyInteger('editable')->nullable()->default(1);
            $table->string('comment')->nullable();
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
