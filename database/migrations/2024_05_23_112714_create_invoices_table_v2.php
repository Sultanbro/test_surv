<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        $this->down();
        Schema::connection('mysql')->create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('amount');
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')
            ->dropIfExists('invoices');
        Schema::dropIfExists('invoices');
    }
};
