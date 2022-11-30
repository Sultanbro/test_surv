<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::create('messenger_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->timestamps();
        });
        // connect message
        Schema::table('messenger_files', function (Blueprint $table) {
            $table->foreign('message_id')->references('id')->on('messenger_messages')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('messenger_files');
    }
};
