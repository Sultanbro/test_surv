<?php

namespace Database\Seeders;

use App\Models\Page;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PageAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->truncatePermissions();

        DB::table('pages')->truncate();
        
        $this->addDroppedForeignKeys();

        $pages = $this->getPages();
        $permissions = $this->getPermissions($pages);

        DB::table('pages')->insert($pages);
        DB::table('permissions')->insert($permissions);
        DB::table('role_has_permissions')->insert($data['role_has_permissions']);
        DB::table('model_has_permissions')->insert($data['model_has_permissions']);
    }


    /**
     * add deleted Foreign Keys
     * 
     * @return void
     */
    private function addDroppedForeignKeys() 
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreign('permission_id')->references('id')->on('permissions');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

     /**
     * truncate Permission constraints
     * 
     * @return array
     */
    private function truncatePermissions() 
    {           
        // model has permissions
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->dropForeign(['permission_id']);
            });

            $modelHasPerms = DB::table('model_has_permissions')->get();

            $items = [];
            foreach($modelHasPerms as $item) {
                $items[] = (array) $item;
            }

            $modelHasPerms = $items;

        // role has permissions
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $table->dropForeign(['permission_id']);
            });
                
            $roleHasPerms = DB::table('role_has_permissions')->get();

            $items = [];
            foreach($roleHasPerms as $item) {
                $items[] = (array) $item;
            }

            $roleHasPerms = $items;

        // truncate
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();

        return [
            'model_has_permissions' => $modelHasPerms,
            'role_has_permissions' => $roleHasPerms,
        ];

    }

    /**
     * get Pages
     * 
     * @return array
     */
    private function getPages() 
    {
        return [
            [
                "id" => 1,
                "name" => "Отчеты",
                "parent_id" => null,
                "key" => "reports"
            ],
//            [
//                "id" => 2,
//                "name" => "ТОП",
//                "parent_id" => 1,
//                "key" => "top"
//            ],
            [
                "id" => 3,
                "name" => "Аналитика",
                "parent_id" => 1,
                "key" => "analytics"
            ],
//            [
//                "id" => 4,
//                "name" => "HR",
//                "parent_id" => 1,
//                "key" => "hr"
//            ],
            [
                "id" => 5,
                "name" => "Табель",
                "parent_id" => 1,
                "key" => "tabel"
            ],
            [
                "id" => 6,
                "name" => "Время прихода",
                "parent_id" => 1,
                "key" => "entertime"
            ],
            [
                "id" => 7,
                "name" => "Начисления",
                "parent_id" => 1,
                "key" => "salaries"
            ],
            [
                "id" => 8,
                "name" => "Контроль качества",
                "parent_id" => 1,
                "key" => "quality"
            ],
            [
                "id" => 9,
                "name" => "Настройки",
                "parent_id" => null,
                "key" => "settings"
            ],
            [
                "id" => 10,
                "name" => "Сотрудники",
                "parent_id" => 9,
                "key" => "users"
            ],
            [
                "id" => 11,
                "name" => "Курсы",
                "parent_id" => 24,
                "key" => "courses"
            ],
            [
                "id" => 12,
                "name" => "Книги",
                "parent_id" => 24,
                "key" => "books"
            ],
            [
                "id" => 13,
                "name" => "Видео",
                "parent_id" => 24,
                "key" => "videos"
            ],
            [
                "id" => 15,
                "name" => "Должности",
                "parent_id" => 9,
                "key" => "positions"
            ],
            [
                "id" => 16,
                "name" => "Отделы",
                "parent_id" => 9,
                "key" => "groups"
            ],
            [
                "id" => 17,
                "name" => "Штрафы",
                "parent_id" => 9,
                "key" => "fines"
            ],
            [
                "id" => 18,
                "name" => "Уведомления",
                "parent_id" => 9,
                "key" => "notifications"
            ],
            [
                "id" => 20,
                "name" => "Чек листы",
                "parent_id" => 9,
                "key" => "checklists"
            ],
            [
                "id" => 21,
                "name" => "Депремирование",
                "parent_id" => null,
                "key" => "penalties"
            ],
            [
                "id" => 22,
                "name" => "ЧАВО",
                "parent_id" => null,
                "key" => "faq"
            ],
            [
                "id" => 23,
                "name" => "Доступы",
                "parent_id" => 9,
                "key" => "permissions"
            ],
            [
                "id" => 24,
                "name" => "Обучение",
                "parent_id" => null,
                "key" => "learning"
            ],
            [
                "id" => 25,
                "name" => "KPI",
                "parent_id" => null,
                "key" => "kpi"
            ],
            [
                "id" => 26,
                "name" => "Новости",
                "parent_id" => null,
                "key" => "news"
            ],
            [
                "id" => 27,
                "name" => "Структура",
                "parent_id" => null,
                "key" => "structure"
            ]
        ];
    }
    
    /**
     * get Permissions
     * 
     * @param array
     * @return array
     */
    private function getPermissions(array $pages) 
    {
        $permissions = [];

        $id = 1;
        
        foreach ($pages as $page) {
            $permissions[] = [
                'id'         => $id++,
                'name'       => $page['key'].'_view',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $permissions[] = [
                'id'         => $id++,
                'name'       => $page['key'].'_edit',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        return $permissions;
    }
}
