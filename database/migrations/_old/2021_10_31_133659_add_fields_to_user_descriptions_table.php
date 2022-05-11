<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUserDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_descriptions', function (Blueprint $table) {
            $table->timestamp('applied')->nullable();
            $table->timestamp('requested')->nullable();
            $table->timestamp('fired')->nullable();
            $table->integer('lead_id')->default(0);
            $table->integer('deal_id')->default(0);
            $table->tinyInteger('bitrix')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
