<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('top_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->date('date');
            $table->string('name');
            $table->string('value');
            $table->string('unit')->default('');
            $table->text('options')->nullable();
            $table->string('min_value');
            $table->string('max_value');
            $table->timestamps();
            $table->integer('activity_id')->default(0);
            $table->integer('round')->default(0);
            $table->integer('is_main')->default(0);
            $table->integer('fixed')->default(0);
            $table->string('value_type', 3)->default('sum');
            $table->string('cell', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('top_values');
    }
}
