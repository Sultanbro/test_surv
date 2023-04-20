<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('messenger_chat_mute', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('user_id');

            $table->primary(['chat_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('messenger_chat_mute');
    }
};
