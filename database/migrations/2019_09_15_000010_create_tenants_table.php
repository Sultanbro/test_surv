<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    protected $connection = 'mysql';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!table_exists('tenants')) {
            Schema::connection('mysql')->create('tenants', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->integer('global_id');
                $table->timestamps();
                $table->json('data')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function down(): void
    {
        if (table_exists('tenant_users', $this->connection) && foreign_exists('tenant_users', 'tenant_users_tenant_id_foreign', $this->connection)) {
            Schema::table('tenant_users', function ($table) {
                $table->dropForeign('tenant_users_tenant_id_foreign');
            });
        }
        if (table_exists('tenant_user_impersonation_tokens', $this->connection) && foreign_exists('tenant_user_impersonation_tokens', 'tenant_user_impersonation_tokens_tenant_id_foreign', $this->connection)) {
            Schema::table('tenant_user_impersonation_tokens', function ($table) {
                $table->dropForeign('tenant_user_impersonation_tokens_tenant_id_foreign');
            });
        }
        Schema::dropIfExists('tenants');
    }
}
