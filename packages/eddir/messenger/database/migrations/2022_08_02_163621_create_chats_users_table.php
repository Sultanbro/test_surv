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
    public function up()
    {
        // many to many relationship between users and chats
        Schema::create('messenger_chat_users', function (Blueprint $table) {
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
    public function down()
    {
        Schema::dropIfExists('chats_users');
    }
};
