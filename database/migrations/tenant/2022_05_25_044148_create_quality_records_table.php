<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('segment_id');
            $table->string('phone', 30);
            $table->string('interlocutor', 40)->nullable();
            $table->integer('day_of_delay')->nullable();
            $table->date('listened_on')->nullable();
            $table->string('params')->default('[]');
            $table->integer('total');
            $table->string('comments')->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->integer('group_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quality_records');
    }
}
