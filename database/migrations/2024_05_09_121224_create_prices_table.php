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
        Schema::connection('mysql')->table('tariff', function (Blueprint $table) {
            if (column_exists('tariff', 'price', 'mysql')) {
                $table->dropColumn('price');
            }
        });

        Schema::connection('mysql')->create('tariff_prices', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('currency');
            $table->foreignId('tariff_id')
                ->references('id')
                ->on('tariff')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('tariff', function (Blueprint $table) {
            $table->decimal('price');
        });

        Schema::connection('mysql')->dropIfExists('tariff_prices');
    }
};
