<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_payment', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->integer('id_user')->default(0)->index();
            $table->string('phone', 100);
            $table->string('phone_owner')->nullable();
            $table->string('payment_from')->default('umar');
            $table->decimal('amount', 10)->nullable();
            $table->decimal('balance', 10)->nullable();
            $table->integer('status')->default(1)->index();
            $table->text('comment')->nullable();
            $table->integer('time')->index();
            $table->string('orderID')->nullable();
            $table->text('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_payment');
    }
}
