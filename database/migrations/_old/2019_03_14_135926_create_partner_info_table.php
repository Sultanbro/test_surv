<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('ceo_name')->nullable();
            $table->string('specialization')->nullable();
            $table->string('specialization_extra')->nullable();
            $table->string('extra_details')->nullable();
            $table->string('logo')->nullable();
            $table->string('card_number')->nullable();
            $table->string('bic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_info');
    }
}
