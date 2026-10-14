<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('group_id');
            $table->string('daily_plan', 11)->default('0');
            $table->string('plan_unit', 20);
            $table->string('unit', 10)->nullable()->default('');
            $table->string('plan_type')->default('sum');
            $table->string('type')->default('default');
            $table->tinyInteger('weekdays')->default(6);
            $table->integer('order')->default(0);
            $table->tinyInteger('editable')->default(1);
            $table->tinyInteger('ud_ves')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->text('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
