<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('profile_groups', 'switch_utility')
            && !Schema::hasColumn('profile_groups', 'switch_proceeds')
            && !Schema::hasColumn('profile_groups', 'switch_rentability')
        ) {
            Schema::table('profile_groups', function (Blueprint $table) {
                $table->tinyInteger('switch_utility')->nullable()->default(1);
                $table->tinyInteger('switch_proceeds')->nullable()->default(1);
                $table->tinyInteger('switch_rentability')->nullable()->default(1);
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
        if (Schema::hasColumn('profile_groups', 'switch_utility')
            && Schema::hasColumn('profile_groups', 'switch_proceeds')
            && Schema::hasColumn('profile_groups', 'switch_rentability')
        ) {
            Schema::table('users', function (Blueprint $table) {
                $columns = [
                      'switch_utility'
                    , 'switch_proceeds'
                    , 'switch_rentability'
                ];
                foreach ($columns as $column) {
                    if (column_exists('users', $column, $this->getConnection())) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
