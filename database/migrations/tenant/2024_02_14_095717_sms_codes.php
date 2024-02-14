<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->integer('code');
            $table->timestamps();
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::table('sms_codes', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
};
