<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->json('payload')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('json');
        });
    }
};
