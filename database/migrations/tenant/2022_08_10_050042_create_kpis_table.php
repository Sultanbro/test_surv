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
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->integer('targetable_id');
            $table->string('targetable_type');
            $table->integer('completed_80')->default(0);
            $table->integer('completed_100')->default(0);
            $table->tinyInteger('lower_limit')->default(0);
            $table->tinyInteger('upper_limit')->default(0);
            $table->jsonb('colors');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
