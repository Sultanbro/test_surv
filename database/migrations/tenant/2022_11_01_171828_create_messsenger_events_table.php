<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::create('messenger_events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
        // connect message
        Schema::table('messenger_events', function (Blueprint $table) {
            $table->foreign('chat_id')->references('id')->on('messenger_chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('messenger_events');
    }
};
