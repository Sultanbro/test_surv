<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDayTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_day_types', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->tinyInteger('type');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('admin_id');
            $table->timestamps();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_day_types');
    }
}
