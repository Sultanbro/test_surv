<?php

use App\Models\CentralUser;
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
        Schema::connection('mysql')
            ->create('tariff_payment', function (Blueprint $table) {
                $table->id();
                $table->integer('extra_user_limit')
                    ->comment('Extra user amount added upon tariff user_limit')
                    ->nullable();
                $table->string('service_for_payment');
                $table->string('status')->nullable();
                $table->date('expire_date')->comment('Срок истечения тарифа');
                $table->boolean('auto_payment')->default(0)->comment('1 - Автоплатеж включен');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->foreignId('owner_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
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
        Schema::connection('mysql')->dropIfExists('tariff_payment');
    }
};
