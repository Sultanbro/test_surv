<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBFileOldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_file_old', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('TIMESTAMP_X')->useCurrentOnUpdate()->useCurrent();
            $table->string('MODULE_ID', 50)->nullable();
            $table->integer('HEIGHT')->nullable();
            $table->integer('WIDTH')->nullable();
            $table->bigInteger('FILE_SIZE')->nullable();
            $table->string('CONTENT_TYPE')->nullable()->default('IMAGE');
            $table->string('SUBDIR')->nullable();
            $table->string('FILE_NAME');
            $table->string('ORIGINAL_NAME')->nullable();
            $table->string('DESCRIPTION')->nullable();
            $table->string('HANDLER_ID', 50)->nullable();
            $table->string('EXTERNAL_ID', 50)->nullable()->index('IX_B_FILE_EXTERNAL_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_file_old');
    }
}
