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
        Schema::create('tax_group_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_group_id');
            $table->string('name');
            $table->tinyInteger('is_percent');
            $table->tinyInteger('end_subtraction');
            $table->integer('value');
            $table->integer('order')->default(1);
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
        Schema::dropIfExists('tax_group_items');
    }
};
