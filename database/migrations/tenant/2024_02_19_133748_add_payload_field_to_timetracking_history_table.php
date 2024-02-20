<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('timetracking_history', function (Blueprint $table) {
            $table->json('payload')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('timetracking_history', function (Blueprint $table) {
            $table->dropColumn('payload');
            $table->dropColumn('deleted_at');
        });
    }
};
