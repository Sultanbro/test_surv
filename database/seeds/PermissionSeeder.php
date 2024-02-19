<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::query()->firstOrCreate(['name' => 'taxes_view', 'guard_name' => 'web']);
        Permission::query()->firstOrCreate(['name' => 'taxes_edit', 'guard_name' => 'web']);

        Permission::query()->firstOrCreate(['name' => 'shifts_view', 'guard_name' => 'web']);
        Permission::query()->firstOrCreate(['name' => 'shifts_edit', 'guard_name' => 'web']);

        $settingsPage = Page::query()->where('key', 'settings')->first();
        if ($settingsPage) {
            Page::query()->firstOrCreate([
                'name' => 'Смены', 'parent_id' => $settingsPage->id, 'key' => 'shifts'
            ]);
            Page::query()->firstOrCreate([
                'name' => 'Налоги', 'parent_id' => $settingsPage->id, 'key' => 'taxes'
            ]);
        } else {
            dump(tenant('id'));
        }
    }
}
