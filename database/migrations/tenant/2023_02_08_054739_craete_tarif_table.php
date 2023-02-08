<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Classes\Enums\TariffValidityEnum;
use App\Classes\Enums\TariffKindEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Name of tariff');
            $table->integer('users_limit')->comment('How many people can be assigned');
            $table->decimal('price')->comment('Price in KZT');
            $table->enum('kind', TariffKindEnum::getAllValues())->comment('Вид тарифа');
            $table->enum('validity', TariffValidityEnum::getAllValues())
                ->comment('Период действия monthly-ежемесячно, annual-ежегодно');
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
        Schema::dropIfExists('tariff');
    }
};
