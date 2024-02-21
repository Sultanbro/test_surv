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
        Schema::table('position', function (Blueprint $table) {
            $table->tinyInteger('ckp_status')->default(0);
            $table->string('ckp')->nullable();
            $table->string('ckp_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('position', function (Blueprint $table) {
            $table->dropColumn('ckp_status');
            $table->dropColumn('ckp');
            $table->dropColumn('ckp_link');
        });
    }
};
