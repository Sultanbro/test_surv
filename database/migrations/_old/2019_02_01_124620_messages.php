<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Messages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('name', 191);
            $table->tinyInteger('is_integration')->default(0);
            $table->text('description')->nullable();
            $table->integer('group_id')->default(0);
            $table->text('message')->nullable();
            $table->string('start_date_time', 191)->nullable();
            $table->string('start_time', 191)->nullable();
            $table->string('end_time', 191)->nullable();
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
        //
    }
}
