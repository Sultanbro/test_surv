<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var Builder
     */
    protected Builder $pages;

    /**
     * @var Builder
     */
    protected Builder $permissions;

    public function __construct()
    {
        $this->pages        = DB::table('pages');
        $this->permissions  = DB::table('permissions');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /**
         * Создаем страницу Награды.
         */
        $this->pages->insert(
            [
                'name' => 'Награды',
                'parent_id' => 9,
                'key' => 'awards'
            ]
        );

        /**
         * Создаем доступы для страницы Награды.
         */
        $this->permissions->insert([
            [
                'name'       => 'awards_edit',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'awards_view',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $this->pages->where('key', 'awards')->delete();
        $this->permissions->whereIn('name', ['award_edit', 'award_view'])->delete();
    }
};
