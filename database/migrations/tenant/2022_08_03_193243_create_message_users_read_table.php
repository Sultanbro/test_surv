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
        // many-to-many relationship between messages and users
        Schema::create('messenger_message_users_read', function (Blueprint $table) {
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['message_id', 'user_id']);
           // $table->foreign('message_id')->references('id')->on('messenger_messages');
           // $table->foreign('user_id')->references('id')->on('users');
        });

        // remove column read from messages table
        Schema::table('messenger_messages', function (Blueprint $table) {
            $table->dropColumn('read');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messenger_message_users_read');
        Schema::table('messenger_messages', function (Blueprint $table) {
            $table->boolean('read')->default(false);
        });
    }
};
