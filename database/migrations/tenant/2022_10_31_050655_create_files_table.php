<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fileable_id')
                ->nullable();
            $table->string('fileable_type')
                ->nullable();
            $table->string('original_name');
            $table->string('local_name');
            $table->string('extension');
            $table->timestamps();

            $table->index(['fileable_id', 'fileable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
