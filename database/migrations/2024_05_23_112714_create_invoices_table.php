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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('gateway');
            $table->string('amount');
            $table->string('currency');
            $table->string('status')->default('pending');
            $table->string('actor_email')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('actor_phone')->nullable();
            $table->timestamps();

            $table->unique(['transaction_id', 'gateway']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
