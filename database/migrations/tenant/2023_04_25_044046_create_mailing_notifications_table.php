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
        Schema::create('mailing_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('created_by');

            $table->foreign('created_by')->on('users')->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->jsonb('type_of_mailing');
            $table->string('frequency')->default('weekly');
            $table->time('time');
            $table->boolean('status')->default(1)->comment('Выкл/Вкл');
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
        Schema::dropIfExists('mailing_notifications');
    }
};
