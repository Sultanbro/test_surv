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
        Schema::table('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_name')->nullable();
            $table->email('email');
            $table->integer('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->integer('position_id')->nullable();
            $table->integer('program_id')->nullable();
        });
    }

            //'full_time',
            //'user_type',
            //'city',
            //'address',
            //'description',
            //'currency',
            //'timezone',
            //'segment',
            //'working_day_id',
            //'working_time_id',
            //'working_country',
            //'working_city',
            //'work_start',
            //'work_end',
            //'birthday', // admin.u-marketing
            //'last_group',
            //'read_corp_book_at',
            //'has_noti',
            //'role_id',
            //'is_admin',
            //'weekdays', // 0000000


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
