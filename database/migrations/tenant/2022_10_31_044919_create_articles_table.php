<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');

            $table->string('title', 125);
            $table->longText('content');
            $table->json('available_for')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('author_id')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
        });

        Schema::dropIfExists('articles');
    }
};
