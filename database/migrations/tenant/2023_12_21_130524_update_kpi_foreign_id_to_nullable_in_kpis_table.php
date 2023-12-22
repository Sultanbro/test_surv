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
        Schema::table('kpis', function (Blueprint $table) {
            $table->integer('targetable_id')
                ->change()
                ->nullable();
            $table->string('targetable_type')
                ->change()
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('kpis', function (Blueprint $table) {
            $table->integer('targetable_id')
                ->nullable(false)
                ->change();
            $table->string('targetable_type')
                ->nullable(false)
                ->change();
        });
    }
};
