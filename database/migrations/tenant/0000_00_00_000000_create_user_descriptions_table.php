<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('fire_cause')->nullable();
            $table->timestamp('fire_date')->nullable();
            $table->string('books')->default('[]');
            $table->timestamps();
            $table->text('quiz_after_fire')->nullable();
            $table->timestamp('applied')->nullable();
            $table->timestamp('requested')->nullable();
            $table->timestamp('fired')->nullable();
            $table->integer('lead_id')->default(0);
            $table->integer('deal_id')->default(0);
            $table->tinyInteger('bitrix')->default(0);
            $table->integer('bitrix_id')->default(0);
            $table->text('rating1')->nullable();
            $table->text('rating2')->nullable();
            $table->tinyInteger('is_trainee')->default(0)->index();
            $table->text('notifications')->nullable();
            $table->text('recruiter_comment')->nullable();
            $table->integer('headphones_amount')->default(0);
            $table->date('headphones_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_descriptions');
    }
}
