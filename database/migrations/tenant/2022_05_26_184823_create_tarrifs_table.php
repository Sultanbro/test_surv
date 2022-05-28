<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarrifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarrifs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre')->default(44);
            $table->integer('prefix');
            $table->tinyInteger('length')->default(11);
            $table->string('direction');
            $table->string('autocall_sip_cost');
            $table->string('autocall_transfer_bill_duration');
            $table->string('autocall_integration_funct_cost');
            $table->string('autocall_integration_transfer_cost');
            $table->string('autocall_integration_cost');
            $table->string('autocall_sms_cost');
            $table->string('autocall_funct_cost');
            $table->string('autocall_transfer_cost');
            $table->string('autocall_cost3');
            $table->string('autocall_cost2');
            $table->string('autocall_cost');
            $table->string('autocall_bill_duration');
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
        Schema::dropIfExists('tarrifs');
    }
}
