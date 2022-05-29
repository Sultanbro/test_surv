<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBUserCallibroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_callibro', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('TIMESTAMP_X')->useCurrentOnUpdate()->useCurrent();
            $table->string('LOGIN', 50);
            $table->string('PASSWORD', 50);
            $table->string('CHECKWORD', 50)->nullable();
            $table->string('remember_token', 191)->nullable();
            $table->char('ACTIVE', 1)->default('Y');
            $table->string('NAME', 50)->nullable();
            $table->string('LAST_NAME', 50)->nullable();
            $table->string('email')->nullable();
            $table->double('bonus', 8, 2)->default(0);
            $table->dateTime('LAST_LOGIN')->nullable();
            $table->dateTime('DATE_REGISTER');
            $table->char('LID', 2)->nullable();
            $table->string('PERSONAL_PROFESSION')->nullable();
            $table->string('PERSONAL_WWW')->nullable();
            $table->string('PERSONAL_ICQ')->nullable();
            $table->char('PERSONAL_GENDER', 1)->nullable();
            $table->string('PERSONAL_BIRTHDATE', 50)->nullable();
            $table->integer('PERSONAL_PHOTO')->nullable();
            $table->string('PERSONAL_PHONE')->nullable();
            $table->string('PERSONAL_FAX')->nullable();
            $table->string('PERSONAL_MOBILE')->nullable();
            $table->string('PERSONAL_PAGER')->nullable();
            $table->text('PERSONAL_STREET')->nullable();
            $table->string('PERSONAL_MAILBOX')->nullable();
            $table->string('PERSONAL_CITY')->nullable();
            $table->string('PERSONAL_STATE')->nullable();
            $table->string('PERSONAL_ZIP')->nullable();
            $table->string('PERSONAL_COUNTRY')->nullable();
            $table->text('PERSONAL_NOTES')->nullable();
            $table->string('WORK_COMPANY')->nullable();
            $table->string('WORK_DEPARTMENT')->nullable();
            $table->string('WORK_POSITION')->nullable();
            $table->string('WORK_WWW')->nullable();
            $table->string('WORK_PHONE')->nullable();
            $table->string('WORK_FAX')->nullable();
            $table->string('WORK_PAGER')->nullable();
            $table->text('WORK_STREET')->nullable();
            $table->string('WORK_MAILBOX')->nullable();
            $table->string('WORK_CITY')->nullable();
            $table->string('WORK_STATE')->nullable();
            $table->string('WORK_ZIP')->nullable();
            $table->string('WORK_COUNTRY')->nullable();
            $table->text('WORK_PROFILE')->nullable();
            $table->integer('WORK_LOGO')->nullable();
            $table->text('WORK_NOTES')->nullable();
            $table->text('ADMIN_NOTES')->nullable();
            $table->string('STORED_HASH', 32)->nullable();
            $table->string('XML_ID')->nullable();
            $table->date('PERSONAL_BIRTHDAY')->nullable();
            $table->string('EXTERNAL_AUTH_ID')->nullable();
            $table->dateTime('CHECKWORD_TIME')->nullable();
            $table->string('SECOND_NAME', 50)->nullable();
            $table->string('CONFIRM_CODE', 8)->nullable();
            $table->integer('LOGIN_ATTEMPTS')->nullable();
            $table->dateTime('LAST_ACTIVITY_DATE')->nullable();
            $table->char('AUTO_TIME_ZONE', 1)->nullable();
            $table->string('TIME_ZONE', 50)->nullable();
            $table->integer('TIME_ZONE_OFFSET')->nullable();
            $table->string('TITLE')->nullable();
            $table->string('BX_USER_ID', 32)->nullable();
            $table->char('LANGUAGE_ID', 2)->nullable();

            $table->unique(['LOGIN', 'EXTERNAL_AUTH_ID'], 'ix_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_callibro');
    }
}
