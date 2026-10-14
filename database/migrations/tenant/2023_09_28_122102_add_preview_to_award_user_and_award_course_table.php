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
        Schema::table('award_user', function (Blueprint $table) {
            $table->string('preview_path')->nullable()->after('format');
            $table->string('preview_format')->nullable()->after('preview_path');
        });

        Schema::table('award_course', function (Blueprint $table) {
            $table->string('preview_path')->nullable()->after('format');
            $table->string('preview_format')->nullable()->after('preview_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('award_user', function (Blueprint $table) {
            $table->dropColumn('preview_path');
            $table->dropColumn('preview_format');
        });

        Schema::table('award_course', function (Blueprint $table) {
            $table->dropColumn('preview_path');
            $table->dropColumn('preview_format');
        });
    }
};
