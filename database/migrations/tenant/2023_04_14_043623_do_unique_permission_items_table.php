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
    public function up(): void
    {
        Schema::table('permission_items', function (Blueprint $table) {
            $table->unique(['targets', 'roles', 'groups']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('permission_items', function (Blueprint $table) {
            $table->dropUnique(['targets', 'roles', 'groups']);
        });
    }
};
