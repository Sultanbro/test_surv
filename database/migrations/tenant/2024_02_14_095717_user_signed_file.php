<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('user_signed_file', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreignId('file_id')
                ->references('id')
                ->on('files')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_signed_file', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
};
