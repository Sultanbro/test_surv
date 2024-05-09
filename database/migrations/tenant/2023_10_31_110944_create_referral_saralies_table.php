<?php

use App\Service\Referral\Core\PaidType;
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
        Schema::create('referral_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId("referrer_id")
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId("referral_id")
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->float("amount");
            $table->boolean("is_paid")
                ->default(0);
            $table->string("comment")
                ->nullable();
            $table->string("type")->default(PaidType::TRAINEE->name);
            $table->string("date");
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
        Schema::dropIfExists('referral_salaries');
    }
};
