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
    public function up()
    {
        Schema::create('quartal_premiums', function (Blueprint $table) {
            $table->id();
            $table->integer('targetable_id');
            $table->string('targetable_type');
            $table->integer('activity_id');
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('plan')->nullable();
            $table->date('from');
            $table->date('to');
            $table->softDeletes();
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
        Schema::dropIfExists('quartal_premiums');
    }
};
