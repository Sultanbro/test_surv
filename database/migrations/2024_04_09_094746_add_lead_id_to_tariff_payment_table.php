<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        if (!column_exists('tariff_payment', 'lead_id', 'mysql')) {
            Schema::connection('mysql')->table('tariff_payment', function (Blueprint $table) {
                $table->string('lead_id')
                    ->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('tariff_payment', function (Blueprint $table) {
            $table->dropColumn('lead_id');
        });
    }
};
