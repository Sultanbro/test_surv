<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->dropColumn('invoice_id');
            $table->dropColumn('gateway');
            $table->string('transaction_id')->nullable();
            $table->unique(['transaction_id', 'gateway']);
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('invoices', function (Blueprint $table) {

            $table->dropColumn('status');
            $table->dropColumn('payment_id');
            $table->dropColumn('gateway');
            $table->string('invoice_id')->nullable();
            $table->dropUnique(['transaction_id', 'gateway']);
        });
    }
};
