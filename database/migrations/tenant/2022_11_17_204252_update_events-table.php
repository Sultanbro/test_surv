<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		// drop table and relations then create new table
        Schema::dropIfExists('messenger_events');
        Schema::create('messenger_events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->unsignedBigInteger('message_id');
            $table->json('payload')->nullable();

            $table->foreign('message_id')->references('id')->on('messenger_messages')->onDelete('cascade');
        });
	}

	public function down() {
		Schema::table( 'messenger_events', function ( Blueprint $table ) {
			// drop message_id
            $table->dropColumn( 'message_id' );
            // add chat_id and user_id
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('chat_id')->references('id')->on('messenger_chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		} );
	}
};
