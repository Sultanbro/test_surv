<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('check_users_id');
            $table->integer('check_id');
            $table->integer('count_check');
            $table->integer('count_check_auth')->default(0);
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->integer('item_type');
            $table->integer('item_id');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_reports');
    }
}
