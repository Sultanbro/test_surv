<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitrixAppBindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitrix_app_bindings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_key')->nullable();
            $table->integer('integration_id')->nullable();
            $table->string('AUTH_ID');
            $table->string('AUTH_EXPIRES');
            $table->string('REFRESH_ID');
            $table->string('member_id');
            $table->string('status');
            $table->string('PLACEMENT');
            $table->string('DOMAIN');
            $table->string('PROTOCOL');
            $table->string('LANG');
            $table->string('APP_SID');
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
        Schema::dropIfExists('bitrix_app_bindings');
    }
}
