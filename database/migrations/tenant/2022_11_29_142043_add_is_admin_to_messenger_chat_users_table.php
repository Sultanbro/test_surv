<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		Schema::table( 'messenger_chat_users', function ( Blueprint $table ) {
            $table->boolean( 'is_admin' )->default( false );
		} );
        // update existing records
        DB::table( 'messenger_chat_users' )->update( [ 'is_admin' => true ] );
	}

	public function down() {
		Schema::table( 'messenger_chat_users', function ( Blueprint $table ) {
			$table->dropColumn( 'is_admin' );
		} );
	}
};
