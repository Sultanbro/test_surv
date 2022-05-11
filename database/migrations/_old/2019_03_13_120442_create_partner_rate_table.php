<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_rate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('service', 15);
            $table->tinyInteger('first_rate');
            $table->tinyInteger('second_rate');
            $table->tinyInteger('third_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_rate');
    }
}
