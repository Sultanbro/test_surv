<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_contacts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_user')->index();
            $table->integer('id_group')->index();
            $table->string('phone')->index();
            $table->text('data');
            $table->tinyInteger('send_or_not')->default(0);
            $table->timestamp('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_contacts');
    }
}
