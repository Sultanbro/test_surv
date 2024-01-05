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
    public function up(): void
    {
        Schema::create('kpiables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')
                ->constrained()
                ->on('kpis')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('kpiable_id');
            $table->string('kpiable_type');
            $table->timestamps();
            $table->index(['kpiable_id', 'kpiable_type'], 'targetable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('kpiables');
    }
};
