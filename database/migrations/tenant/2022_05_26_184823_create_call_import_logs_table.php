<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallImportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_import_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('import_user_id');
            $table->integer('import_group_id');
            $table->string('import_file', 128);
            $table->string('import_status', 32)->nullable();
            $table->dateTime('import_date')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_import_logs');
    }
}
