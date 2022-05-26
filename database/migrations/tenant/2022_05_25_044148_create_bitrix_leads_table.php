<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitrixLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitrix_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->integer('deal_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('resp_id')->nullable()->default('0');
            $table->integer('segment')->default(0);
            $table->string('phone', 30);
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('skype', 100)->nullable();
            $table->string('project')->nullable()->default('0');
            $table->string('status', 50);
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('lang')->nullable();
            $table->string('house')->nullable();
            $table->string('net')->nullable();
            $table->string('files')->default('[]');
            $table->string('hash')->nullable();
            $table->integer('signed')->default(0);
            $table->timestamp('time')->nullable();
            $table->string('city')->nullable();
            $table->string('age')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('wishtime')->nullable();
            $table->tinyInteger('received_assessment')->default(0);
            $table->integer('invited')->nullable()->default(0);
            $table->timestamp('skyped')->nullable();
            $table->timestamp('inhouse')->nullable();
            $table->integer('invite_group_id')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamp('rating_date')->nullable();
            $table->integer('rating2')->nullable();
            $table->timestamp('rating2_date')->nullable();
            $table->timestamp('invite_at')->nullable();
            $table->timestamp('day_second')->nullable();
            $table->integer('received_fd')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitrix_leads');
    }
}
