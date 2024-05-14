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
    public function up(): void
    {
        Schema::create('mailing_notification_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notificationable_id');
            $table->string('notificationable_type');
            $table->unsignedBigInteger('notification_id');

            $table->foreign('notification_id')->on('mailing_notifications')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('mailing_notification_schedules');
    }
};
