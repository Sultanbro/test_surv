<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRespInhouseTimeToBitrixLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bitrix_leads', function (Blueprint $table) {
            $table->integer('resp_id')->default(0)->after('user_id');
            $table->timestamp('inhouse')->nullable()->after('skyped');
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
