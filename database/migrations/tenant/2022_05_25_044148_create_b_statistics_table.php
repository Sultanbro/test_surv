<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_statistics', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_belong')->default(0)->index();
            $table->integer('id_user')->default(0)->index();
            $table->integer('id_contact')->default(0)->index();
            $table->string('id_session', 100)->nullable()->index();
            $table->string('number', 100);
            $table->timestamp('date')->nullable()->index();
            $table->float('cost', 10, 0);
            $table->integer('status')->index();
            $table->string('type', 100)->index('type');
            $table->string('line', 100);
            $table->text('text');
            $table->text('data')->nullable();
            $table->smallInteger('is_laravel')->default(0);
            $table->smallInteger('has_sms')->default(0);
            $table->smallInteger('call_attempt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_statistics');
    }
}
