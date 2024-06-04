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
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('provider')->default('unknown');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('url');
            $table->dropColumn('provider');
        });
    }
};
