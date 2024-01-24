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
        if (!table_exists('tenant_pivot', $this->getConnection())) {
            Schema::connection('mysql')->create('tenant_pivot', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tenant_id');
                $table->integer('user_id');
                $table->tinyInteger('owner')->default(0);
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
        Schema::connection('mysql')->dropIfExists('tenant_pivot');
    }
};
