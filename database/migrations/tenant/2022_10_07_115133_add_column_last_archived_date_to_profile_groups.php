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
        Schema::table('profile_groups', function (Blueprint $table) {
            if (!column_exists('profile_groups', 'archived_date')) {
                $table->date('archived_date')->nullable();
            }
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
            $table->dropColumn('archived_date');
        });
    }
};
