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
        if (!table_exists('central_course_cats', $this->getConnection())) {
            Schema::connection('mysql')->create('central_course_cats', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('order');
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
        Schema::dropIfExists('central_course_cats');
    }
};
