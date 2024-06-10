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
        if (column_exists('quartal_premiums', 'sum')) return;
        Schema::table('quartal_premiums', function (Blueprint $table) {

            $table->string('sum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quartal_premiums', function (Blueprint $table) {
            $table->dropColumn('sum');
        });
    }
};
