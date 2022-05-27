<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadhunterNegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headhunter_negotiations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vacancy_id');
            $table->bigInteger('negotiation_id');
            $table->integer('lead_id')->nullable();
            $table->timestamp('time')->useCurrentOnUpdate()->useCurrent();
            $table->string('name');
            $table->string('phone');
            $table->string('resume_id');
            $table->integer('has_updated')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('headhunter_negotiations');
    }
}
