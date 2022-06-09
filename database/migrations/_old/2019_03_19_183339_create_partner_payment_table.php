<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('partner_user');
            $table->decimal('amount', 10, 2);
            $table->string('contact_name')->nullable();
            $table->string('card_number')->nullable();
            $table->string('bic')->nullable();
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
        Schema::dropIfExists('partner_payment');
    }
}
