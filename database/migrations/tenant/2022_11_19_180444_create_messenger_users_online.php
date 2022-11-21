<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		Schema::create( 'messenger_users_online', function ( Blueprint $table ) {
			$table->id();
            $table->unsignedBigInteger( 'user_id' )->nullable();
            $table->string( 'socket_id' );
            $table->unsignedBigInteger( 'resource_id' )->nullable();
            $table->timestamp( 'last_seen' )->useCurrent();

            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->onDelete( 'cascade' );
		} );
	}

	public function down() {
		Schema::dropIfExists( 'messenger_users_online' );
	}
};
