<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 100);
            $table->string('phone', 50);
            $table->string('password', 100);
            $table->string('name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('remember_token', 191);
            $table->integer('position_id')->nullable();
            $table->integer('program_id')->nullable();
            $table->tinyInteger('full_time')->default(1);
            $table->string('user_type')->default('office');
            $table->string('city', 191);
            $table->text('address');
            $table->text('description');
            $table->string('currency', 191)->default('KZT');
            $table->integer('timezone')->default(6);
            $table->integer('segment')->default(0);
            $table->unsignedInteger('working_day_id')->nullable();
            $table->unsignedInteger('working_time_id')->nullable();
            $table->time('work_start');
            $table->time('work_end');
            $table->dateTime('birthday')->nullable();
            $table->timestamp('read_corp_book_at');
            $table->tinyInteger('has_noti')->default(0);
            $table->string('weekdays', 7)->default('0000000');
            $table->integer('role_id')->default(1);
            $table->integer('is_admin', 0);
            $table->tinyInteger('groups_all');
            $table->string('working_country', 125);
            $table->string('working_city', 125);
            $table->timestamp('applied_at');
            $table->string('img_url', 191);
            $table->string('phone_1', 50);
            $table->string('phone_2', 50);
            $table->string('phone_3', 50);
            $table->string('phone_4', 50);
            $table->integer('headphones_sum');
            $table->timestamp('notified_at');
            $table->tinyInteger('active_status', 0);
            $table->string('avatar', 125)->nullable();
            $table->string('cropped_img_url', 150);
            $table->timestamps();
//            $table->timestamp('applied_at')->nullable();
            $table->softDeletes();
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
