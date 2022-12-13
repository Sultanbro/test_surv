<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		Schema::table( 'messenger_messages', function ( Blueprint $table ) {
            $table->unsignedBigInteger( 'parent_id' )->nullable()->after( 'chat_id' );
            $table->foreign( 'parent_id' )->references( 'id' )->on( 'messenger_messages' )->onDelete( 'cascade' );
		} );
	}

	public function down() {
		Schema::table( 'messenger_messages', function ( Blueprint $table ) {
			$table->dropForeign( [ 'parent_id' ] );
            $table->dropColumn( 'parent_id' );
		} );
	}
};
