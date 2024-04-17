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
        Schema::create('user_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_group_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('status');
            $table->date('from');
            $table->date('to')->nullable();
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
        Schema::dropIfExists('user_taxes');
    }
};
