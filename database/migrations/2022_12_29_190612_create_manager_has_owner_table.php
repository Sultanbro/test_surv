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
        Schema::connection('mysql')->create('manager_has_owner', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('owner_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('manager_has_owner');
    }
};
