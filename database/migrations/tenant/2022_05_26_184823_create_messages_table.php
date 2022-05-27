<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('name', 191);
            $table->tinyInteger('is_integration')->default(0);
            $table->string('direction_country_prefix', 191)->nullable();
            $table->text('description')->nullable();
            $table->string('group_id', 191)->default('0');
            $table->text('message')->nullable();
            $table->string('start_date_time', 191)->nullable();
            $table->string('start_time', 191)->nullable();
            $table->string('end_time', 191)->nullable();
            $table->string('sms_repeat', 191)->nullable();
            $table->text('sms_repeat_info')->nullable();
            $table->string('total_cost', 191)->nullable();
            $table->tinyInteger('send_remaining_balance')->default(0);
            $table->integer('count_day')->default(0);
            $table->tinyInteger('early')->default(0);
            $table->tinyInteger('latin')->default(0);
            $table->string('multi_name', 191)->nullable();
            $table->tinyInteger('standard_multi_name')->default(0);
            $table->tinyInteger('process_kcell')->default(0);
            $table->tinyInteger('process_beeline')->default(0);
            $table->tinyInteger('process_tele2')->default(0);
            $table->tinyInteger('process_altel')->default(0);
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
        Schema::dropIfExists('messages');
    }
}
