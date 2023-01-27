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
        if (!Schema::hasColumn('test_results', 'course_item_model_id'))
        {
            Schema::table('test_results', function (Blueprint $table) {
                $table->integer('course_item_model_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('test_results', 'course_item_model_id')) {
            Schema::table('test_results', function (Blueprint $table) {
                $table->dropColumn('course_item_model_id');
            });
        }
    }
};
