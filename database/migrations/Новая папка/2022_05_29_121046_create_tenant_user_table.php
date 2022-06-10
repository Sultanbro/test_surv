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
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tenant_id');
            $table->integer('user_id');

            // $table->foreignId('tenant_id')->constrained();
            // $table->foreignId('user_id')->constrained();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('tenant_users', function (Blueprint $table) {
        //     //
        // });
    }
};
