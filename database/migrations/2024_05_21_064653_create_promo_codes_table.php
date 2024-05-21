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
        Schema::connection('mysql')->create('promo_codes', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('rate')->default(20);
            $table->string('expired_at');
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
        Schema::connection('mysql')->dropIfExists('promo_codes');
    }
};