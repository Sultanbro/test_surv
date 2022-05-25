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
        Schema::create('b_user', function (Blueprint $table) {
            $table->integer('ID', true);
            $table->timestamp('TIMESTAMP_X')->useCurrentOnUpdate()->useCurrent();
            $table->string('EMAIL')->nullable()->unique('ix_b_user_email');
            $table->string('PASSWORD', 50);
            $table->string('CHECKWORD', 50)->nullable();
            $table->string('AUTH_TOKEN', 191)->nullable();
            $table->string('NAME', 50)->nullable();
            $table->string('LAST_NAME', 50)->nullable();
            $table->integer('UF_ADMIN')->nullable()->default(0)->index('b_user_UF_ADMIN_index');
            $table->char('ACTIVE', 1)->default('Y');
            $table->integer('position_id')->nullable();
            $table->unsignedInteger('program_id')->nullable()->index('fk_program_id');
            $table->tinyInteger('full_time')->nullable()->default(1);
            $table->string('user_type')->nullable();
            $table->string('PHONE')->nullable();
            $table->string('CITY')->nullable();
            $table->text('ADDRESS')->nullable();
            $table->string('COMPANY')->nullable();
            $table->text('DESCRIPTION')->nullable();
            $table->string('BALANCE_NOTIFY')->nullable();
            $table->tinyInteger('notify_sent')->default(0);
            $table->double('bonus', 8, 2)->default(0);
            $table->timestamp('AUTH_TIME')->nullable();
            $table->timestamp('LAST_LOGIN')->nullable();
            $table->timestamp('DATE_REGISTER')->nullable();
            $table->integer('max_sessions')->nullable();
            $table->string('currency')->default('kzt');
            $table->text('menu')->nullable();
            $table->text('UF_API_KEY')->nullable();
            $table->text('UF_SIP_ACC')->nullable();
            $table->text('UF_BALANCE')->nullable();
            $table->text('UF_SMPP')->nullable();
            $table->text('UF_LOGO')->nullable();
            $table->integer('timezone')->default(6);
            $table->tinyInteger('reminder_sent')->default(0);
            $table->tinyInteger('audio_reminder_sent')->default(0);
            $table->text('roles')->nullable();
            $table->string('books', 191)->nullable();
            $table->integer('segment')->nullable()->default(0);
            $table->unsignedInteger('working_day_id')->nullable()->default(2)->index('fk_working_day_id');
            $table->unsignedInteger('working_time_id')->nullable()->default(2)->index('fk_working_time_id');
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('phone_4')->nullable();
            $table->tinyInteger('active_in_admin')->default(1)->comment('Поле для определения можно ли пользователя входить в админку');
            $table->timestamp('deactivate_date')->nullable()->comment('Дата увольнения');
            $table->softDeletes()->comment('Дата удаления');
            $table->dateTime('birthday')->nullable();
            $table->string('last_group', 100)->nullable()->default('[]');
            $table->timestamp('read_corp_book_at')->nullable();
            $table->integer('has_noti')->default(0);
            $table->string('weekdays', 7)->default('0000000');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_user');
    }
}
