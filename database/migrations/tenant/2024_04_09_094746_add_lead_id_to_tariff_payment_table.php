<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('tariff_payment', function (Blueprint $table) {
            $table->string('lead_id')->after('tariff_id');
        });
    }

    public function down(): void
    {
        Schema::table('tariff_payment', function (Blueprint $table) {
            $table->dropColumn('lead_id');
        });
    }
};
