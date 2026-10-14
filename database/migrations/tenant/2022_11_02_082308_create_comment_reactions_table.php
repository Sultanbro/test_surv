<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('user_id');
            $table->string('reaction');

            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('comment_id')->on('comments')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('comment_reactions_users', function (Blueprint $table) {
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('comment_reactions');
    }
};
