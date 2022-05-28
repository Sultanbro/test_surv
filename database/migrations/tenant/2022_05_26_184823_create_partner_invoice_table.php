<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('partner_user');
            $table->string('service_name');
            $table->decimal('amount', 10);
            $table->string('country')->default('kz');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('partner_invoice');
    }
}
