<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
       // Schema::dropIfExists('headhunter_negotiations');
    }
}
