<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		Schema::table( 'messenger_chat_users', function ( Blueprint $table ) {
            $table->boolean( 'pinned' )->default( false );
		} );
	}

	public function down() {
		Schema::table( 'messenger_chat_users', function ( Blueprint $table ) {
			$table->dropColumn( 'pinned' );
		} );
	}
};
