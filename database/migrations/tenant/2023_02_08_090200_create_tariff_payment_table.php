<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id')
                ->unsigned()
                ->comment('User with is_admin=true');
            $table->unsignedBigInteger('tariff_id')->unsigned();
            $table->integer('extra_user_limit')
                ->comment('Extra user amount added upon tariff user_limit')
                ->nullable();
            $table->date('expire_date')->comment('Срок истечения тарифа');
            $table->boolean('auto_payment')->default(0)->comment('1 - Автоплатеж включен');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('tariff_id')
                ->references('id')
                ->on('tariff')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariff_payment');
    }
};
