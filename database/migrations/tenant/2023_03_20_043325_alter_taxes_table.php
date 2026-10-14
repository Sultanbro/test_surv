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
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->float('amount')->change();
            $table->renameColumn('amount', 'value');
            $table->boolean('percent')->change();
            $table->renameColumn('percent', 'is_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->integer('value')->change();
            $table->renameColumn('value', 'amount');
            $table->integer('is_percent')->change();
            $table->renameColumn('is_percent', 'percent');
        });
    }
};
