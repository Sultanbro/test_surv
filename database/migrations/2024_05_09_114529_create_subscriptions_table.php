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
        if (Schema::connection('mysql')->hasTable('subscriptions')) return;
        Schema::connection('mysql')->create('tariff_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('extra_user_limit');
            $table->string('payment_provider');
            $table->string('payment_id')->nullable();
            $table->string('status')->nullable();
            $table->date('expire_date');
            $table->foreignId('tariff_id')
                ->references('id')
                ->on('tariff')
                ->cascadeOnDelete();
            $table->string('lead_id')
                ->nullable();
            $table->string('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('tariff_subscriptions');
    }
};
