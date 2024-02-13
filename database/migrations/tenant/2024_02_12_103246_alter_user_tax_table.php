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
        Schema::table('user_tax', function (Blueprint $table) {
            $table->tinyInteger('end_subtraction')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->date('from')->nullable();
            $table->date('to')->nullable();
        });

        $taxes = DB::table('taxes')->where('end_subtraction', 1)->pluck('id')->toArray();
        DB::table('user_tax')->whereIn('tax_id', $taxes)->update(['end_subtraction' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tax', function (Blueprint $table) {
            $table->dropColumn('end_subtraction');
            $table->dropColumn('status');
            $table->dropColumn('from');
            $table->dropColumn('to');
        });
    }
};
