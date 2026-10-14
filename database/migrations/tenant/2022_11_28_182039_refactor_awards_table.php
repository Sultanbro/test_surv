<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('awards', function (Blueprint $table) {
            $columnsToDelete = [
                  'name'
                , 'description'
                , 'award_type_id'
                , 'course_id'
                , 'icon'
                , 'hide'
            ];

            foreach ($columnsToDelete as $column) {
                if (column_exists('awards', $column, $this->getConnection())) {
                    $table->dropColumn($column);
                }
            }
            if (!column_exists('awards', 'award_category_id', $this->getConnection())) {
                $table->unsignedBigInteger('award_category_id');
            }
            if (!foreign_exists('awards', 'award_category_id', $this->getConnection())) {
                $table->foreign('award_category_id')->on('award_categories')->references('id')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {

            if (!column_exists('awards', 'name', $this->getConnection())) {
                $table->string('name');
            }
            if (!column_exists('awards', 'description', $this->getConnection())) {
                $table->string('description');
            }
            if (!column_exists('award_type_id', 'award_type_id', $this->getConnection())) {
                $table->integer('award_type_id');
            }
            if (!column_exists('awards', 'hide', $this->getConnection())) {
                $table->tinyInteger('hide');
            }
            if (!column_exists('awards', 'course_id', $this->getConnection())) {
                $table->integer('course_id');
            }
        });

        Schema::table('awards', function (Blueprint $table) {
            if (foreign_exists('awards', 'award_category_id', $this->getConnection())) {
                $table->dropForeign('award_category_id');
            }
        });
    }
};
