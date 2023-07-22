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
        if (!Schema::hasColumn('headhunter_negotiations', 'from'))
        {
            Schema::table('headhunter_negotiations', function (Blueprint $table) {
                $table->tinyInteger('from')->nullable()->comment("hh=1 или hh2=2");
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
        if (Schema::hasColumn('headhunter_negotiations', 'from'))
        {
            Schema::table('headhunter_negotiations', function (Blueprint $table) {
                $table->dropColumn('from');
            });
        }
    }
};
