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
        Schema::table('messenger_messages', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('chat_id')->references('id')->on('messenger_chats');
        });

        Schema::table('messenger_chats', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users');
        });

        Schema::table('messenger_message_users_read', function (Blueprint $table) {
           $table->foreign('message_id')->references('id')->on('messenger_messages');
           $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messenger_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['chat_id']);
        });

        Schema::table('messenger_chats', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });
    }
};
