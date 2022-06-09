<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('type')->default('normal');
            $table->string('title');
            $table->text('message');
            $table->string('image')->nullable();
            $table->tinyInteger('email')->default(0);
            $table->integer('total_sent');
            $table->integer('emails_sent')->default(0);
            $table->integer('read')->default(0);
            $table->timestamps();
        });

        Schema::create('read_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');

        Schema::dropIfExists('read_notifications');
    }
}
