<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 191)->nullable();
            $table->integer('position_id')->nullable();
            $table->unsignedInteger('program_id')->nullable();
            $table->tinyInteger('full_time')->nullable()->default(1);
            $table->string('user_type')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('currency')->default('kzt');
            $table->integer('timezone')->default(6);
            $table->integer('segment')->nullable()->default(0);
            $table->unsignedInteger('working_day_id')->nullable()->default(2);
            $table->unsignedInteger('working_time_id')->nullable()->default(2);
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('last_group', 100)->nullable()->default('[]');
            $table->timestamp('read_corp_book_at')->nullable();
            $table->integer('has_noti')->default(0);
            $table->string('weekdays', 7)->default('0000000');
            $table->timestamps();
            $table->softDeletes()->comment('Дата удаления');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
