<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthClientTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_client_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('server', 30);
            $table->string('domain', 100);
            $table->unsignedInteger('user_id');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->useCurrentOnUpdate()->useCurrent();
            $table->text('auth_code')->nullable();
            $table->string('grant_type', 50);
            $table->string('scope', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_client_tokens');
    }
}
