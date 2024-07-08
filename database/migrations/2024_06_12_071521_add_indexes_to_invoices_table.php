<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->unique('transaction_id');
            $table->index(['transaction_id', 'status', 'provider']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->dropUnique(['transaction_id']);
            $table->dropIndex(['transaction_id', 'status', 'provider']);
            $table->dropIndex(['created_at']);
        });
    }
};
