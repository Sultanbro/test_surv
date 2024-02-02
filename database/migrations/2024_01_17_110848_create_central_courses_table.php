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
        if (!table_exists('central_courses', $this->getConnection())) {
            Schema::connection('mysql')->create('central_courses', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->tinyInteger('for_sale');
                $table->unsignedBigInteger('cat_id')->nullable();
                $table->integer('price')->nullable();
                $table->string('author')->nullable();
                $table->json('slides')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('central_courses');
    }
};
