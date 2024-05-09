<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('quartal_premiums', function (Blueprint $table) {
            $table->string('method')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('quartal_premiums', function (Blueprint $table) {
            $table->dropColumn('method');
        });
    }
};
