<?php

use App\Enums\Tariff\TariffKindEnum;
use App\Enums\Tariff\TariffValidityEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::connection('mysql')->dropIfExists('tariff');
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::connection('mysql')
            ->create('tariff', function (Blueprint $table) {
                $table->id();
                $table->string('kind')->default(TariffKindEnum::Base->value)->comment('Вид тарифа');
                $table->string('validity')->default(TariffValidityEnum::Monthly->value)
                    ->comment('Период действия monthly-ежемесячно, annual-ежегодно');
                $table->integer('users_limit')->comment('How many people can be assigned');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('tariff');
    }
};
