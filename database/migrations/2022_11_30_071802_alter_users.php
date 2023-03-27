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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_at')) {
                $table->timestamp('login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'birthday')) {
                $table->timestamp('birthday')->nullable();
            }
            if (!Schema::hasColumn('users', 'balance')) {
                $table->integer('balance');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'lead')) {
                $table->string('lead')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
