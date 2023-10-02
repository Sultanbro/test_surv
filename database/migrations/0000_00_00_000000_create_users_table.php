<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 50)->nullable();
            $table->string('phone', 50);
            $table->string('name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('password', 150)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->timestamp('birthday')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('lead', 191)->nullable();
            $table->string('country', 50)->nullable();
            $table->integer('balance')->nullable();
            $table->string('currency', 3);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('users');
    }
};
