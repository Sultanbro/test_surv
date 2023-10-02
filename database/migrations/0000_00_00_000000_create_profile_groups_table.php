<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('profile_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->text('users')->nullable();
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            $table->unsignedTinyInteger('workdays')->default(6);
            $table->string('editors_id', 255)->nullable();
            $table->text('book_groups')->nullable();
            $table->integer('required')->default(0);
            $table->integer('provided')->default(0);
            $table->json('head_id');
            $table->string('zoom_link', 255)->nullable();
            $table->string('bp_link', 255)->nullable();
            $table->text('corp_books')->nullable();
            $table->timestamp('checktime')->nullable();
            $table->text('checktime_users')->nullable();
            $table->text('payment_terms')->nullable();
            $table->integer('salary_approved')->default(0);
            $table->integer('salary_approved_by')->default(0);
            $table->timestamp('salary_approved_date')->nullable();
            $table->tinyInteger('has_analytics')->default(0);
            $table->string('quality', 20)->default('ucalls');
            $table->tinyInteger('editable_time')->default(0);
            $table->integer('time_address')->default(0);
            $table->text('time_exceptions')->nullable();
            $table->tinyInteger('paid_internship')->default(0);
            $table->integer('rentability_max')->default(120);
            $table->tinyInteger('show_payment_terms')->default(1);
            $table->date('archived_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('profile_groups');
    }
};
