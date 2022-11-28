<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('likeable_id')->nullable();
            $table->string('likeable_type')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index(['likeable_id', 'likeable_type']);

            $table->foreign('user_id')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('likes');
    }
};
