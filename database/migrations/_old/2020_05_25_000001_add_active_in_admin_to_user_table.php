<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveInUserToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('active_in_admin')->after('working_time_id')->default(1)->comment('Поле для определения можно ли пользователя входить в админку')->nullable();
            $table->dateTime('deactivate_date')->after('active_in_admin')->comment('Дата увольнения')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active_in_admin');
            $table->dropColumn('deactivate_date');
        });
    }
}
