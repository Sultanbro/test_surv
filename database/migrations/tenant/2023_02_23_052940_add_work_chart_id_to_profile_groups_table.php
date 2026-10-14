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
        Schema::table('profile_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('work_chart_id')
                ->nullable()
                ->default(0)
                ->comment('По умолчанию в каждой группе график 5-2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_groups', function (Blueprint $table) {
            $table->dropColumn('work_chart_id');
        });
    }
};
