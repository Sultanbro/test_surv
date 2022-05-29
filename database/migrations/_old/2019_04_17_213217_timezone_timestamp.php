<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimezoneTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('calls', function (Blueprint $table) {
            $table->timestamp('operator_time')->nullable()->change();
            $table->timestamp('start_time')->nullable()->change();
        });*/
        DB::statement("ALTER TABLE `b_contacts` CHANGE `date` `date` TIMESTAMP NULL ");
        DB::statement("ALTER TABLE `users` CHANGE `LAST_LOGIN` `LAST_LOGIN` TIMESTAMP NULL ");
        DB::statement("ALTER TABLE `users` CHANGE `created_at` `created_at` TIMESTAMP NULL ");
        DB::statement("ALTER TABLE `b_statistics` CHANGE `date` `date` TIMESTAMP NULL ");

        //DB::statement('ALTER TABLE `calls` MODIFY `operator_time` TIMESTAMP NULL;');
        //DB::statement('ALTER TABLE `calls` MODIFY `start_time` TIMESTAMP NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
