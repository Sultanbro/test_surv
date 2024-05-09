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
        Schema::connection('mysql')->table('portals', function (Blueprint $table) {
            $table->string('main_page_video')->nullable();
            $table->integer('main_page_video_show_days_amount')
                ->unsigned()
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->table('portals', function (Blueprint $table) {
            $table->dropColumn('main_page_video');
            $table->dropColumn('main_page_video_show_days_amount');
        });
    }
};
