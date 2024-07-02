<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table( 'messenger_message_users_read', function ( Blueprint $table ) {
            // save reaction as number
            $table->unsignedTinyInteger( 'reaction' )->nullable();
        } );
    }

    public function down() {
        Schema::table( 'messenger_message_users_read', function ( Blueprint $table ) {
            $table->dropColumn( 'reaction' );
        } );
    }
};
