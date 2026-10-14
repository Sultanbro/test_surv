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
    public function up(): void
    {
        Schema::table('mailing_notifications', function (Blueprint $table) {
            $table->integer('count')->default(1);
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('mailing_notifications', function (Blueprint $table) {
            if (column_exists('mailing_notifications', 'count', $this->getConnection())) {
                $table->dropColumn('count');
            }
            if (column_exists('mailing_notifications', 'name', $this->getConnection())) {
                $table->dropUnique('name');
            }
        });
    }
};
