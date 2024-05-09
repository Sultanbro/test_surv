<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->text('users')->nullable();
            $table->timestamps();
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            $table->unsignedTinyInteger('workdays')->default(6);
            $table->string('editors_id')->nullable();
            $table->text('book_groups')->nullable();
            $table->integer('required')->default(0);
            $table->integer('provided')->default(0);
            $table->string('head_id')->nullable()->default('[]');
            $table->string('zoom_link')->nullable();
            $table->string('bp_link')->nullable();
            $table->text('corp_books')->nullable();
            $table->timestamp('checktime')->nullable();
            $table->text('checktime_users')->nullable();
            $table->text('payment_terms')->nullable();
            $table->integer('salary_approved')->nullable()->default(0);
            $table->integer('salary_approved_by')->nullable()->default(0);
            $table->timestamp('salary_approved_date')->nullable();
            $table->tinyInteger('has_analytics')->default(0);
            $table->string('quality', 20)->default('ucalls');
            $table->tinyInteger('editable_time')->default(0);
            $table->tinyInteger('time_address')->default(0);
            $table->text('time_exceptions')->nullable();
            $table->tinyInteger('paid_internship')->default(0);
            $table->integer('rentability_max')->default(120);
            $table->tinyInteger('show_payment_terms')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_groups');
    }
}
