<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table( 'messenger_users_online', function ( Blueprint $table ) {
            $table->string( 'domain' )->nullable();
        } );
    }

    public function down() {
        Schema::table( 'messenger_users_online', function ( Blueprint $table ) {
            $table->dropColumn( 'domain' );
        } );
    }
};
