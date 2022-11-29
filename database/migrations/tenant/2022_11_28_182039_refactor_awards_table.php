<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('award_type_id');
            $table->dropColumn('course_id');
            $table->dropColumn('icon');
            $table->dropColumn('hide');

            $table->foreign('award_category_id')->on('award_categories')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('awards', function (Blueprint $table) {

            $table->string('name');
            $table->string('description');
            $table->string('icon');
            $table->integer('award_type_id');
            $table->tinyInteger('hide');
            $table->integer('course_id');

            $table->dropForeign('award_category_id');
        });
    }
};
