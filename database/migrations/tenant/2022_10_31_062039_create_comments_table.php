<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration {
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('article_id');

            $table->text('content');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->on('comments')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('user_id')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('article_id')->on('articles')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('comments');
    }
};
