<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!table_exists('portals', $this->getConnection())) {
            Schema::connection('mysql')->create('portals', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->unsignedBigInteger('owner_id');
                $table->string('currency')->default('kzt');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql')->dropIfExists('portals');
    }
};
