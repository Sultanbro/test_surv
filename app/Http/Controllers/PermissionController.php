<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\ProfileGroup;

class PermissionController extends Controller
{
    public function index(Request $request)
    {   
        View::share('menu', 'permissions');
        View::share('link', 'permissions');

        return view('admin.permissions');
    }

    public function get(Request $request)
    {   
        $roles = Role::with('permissions')->get(['name', 'id']); 



        foreach($roles as $role) {
            $arr = [];
            foreach ($role->permissions as $key => $perm) {
                $arr[$perm->name] = true;
            }

            $role->perms = $arr;
        }

        $items = [];
        // this.items = [ 
        //     {
        //       user: {
        //         id: 5,
        //         name: 'ALi'
        //       },
        //       role: {
        //         id: 1,
        //         name: 'writerro'
        //       },
        //       groups: [
        //         {
        //           id: 42,
        //           name: 'Kaspi'
        //         }
        //       ]
        //     }
        //   ];
        
        return [
            'users' => \App\User::whereNull('deleted_at')->get([\DB::raw("CONCAT(last_name,' ',name) as name"), 'id']),
            'groups' => \App\ProfileGroup::get(['name', 'id']),
            'roles' => $roles,
            'pages' => $this->getPages(),
            'items' => $items
        ];
    }

    private function getPages()
    {
        return [
            ['key' => 'top', 'name' =>  'ТОП'],
            ['key' => 'hr','name'=> 'HR'],
            ['key' => 'analytics','name'=> 'Аналитика'],
            ['key' => 'tabel','name'=> 'Табель'],
            ['key' => 'entertime','name'=> 'Время прихода'],
            ['key' => 'salaries','name'=> 'Начисления'],
            ['key' => 'quality','name'=> 'Контроль качества'],
            ['key' => 'settings','name'=> 'Настройки'],
            ['key' => 'users','name'=> 'Сотрудники'],
            ['key' => 'courses','name'=> 'Курсы'],
            ['key' => 'books','name'=> 'Книги'],
            ['key' => 'videos','name'=> 'Видеокурсы'],
            ['key' => 'kb','name'=> 'База знаний'],
        ];
    }

    public function updateUser(Request $request) {

        $user = User::withTrashed()->find($request->item['user']['id']);
        
        if($user) {
            $this->assignGroups($user->id, $request->item['groups']);
            $role = Role::find($request->item['role']['id']);
            if($role) $user->assignRole($role->name);
        }
    }

    private function assignGroups($user_id, $items)
    {
        if($items == null) $items = [];
        $groups = ProfileGroup::get();

        foreach ($groups as $key => $group) {
            $editors_id = json_decode($group->editors_id);
            if($group->editors_id == null) $editors_id = [];

            if(in_array($group->id, $items)) {
                array_push($editors_id, $user_id);
            } else {
                $editors_id = array_diff($editors_id, [$user_id]);
                $editors_id = array_values($editors_id);
            }

            $group->editors_id = json_encode(array_unique($editors_id));
            $group->save();

        }
    }

    public function createRole(Request $request) {
        return Role::create(['name' => 'role_' . uniqid()]);
    }

    public function updateRole(Request $request) {

        if($request->role['id']) {
            $role = Role::find($request->role['id']);
        } else {
            $role = Role::create(['name' => $request->role['name']]);
        }
        
        if(!$role) return '';

        $role->name = $request->role['name'];
        $role->save();

        foreach ($this->getPages() as $key => $page) {
            
            $permission = $page['key'] . '_view';
            // dump($permission);
            // dump($request->permissions);
            if(in_array($permission, $request->permissions)) {
                $role->givePermissionTo($permission);       
            } else {
             
                $role->revokePermissionTo($permission);
            }
            
            $permission = $page['key'] . '_edit';
            if(in_array($permission, $request->permissions)) {
                $role->givePermissionTo($permission);          
            } else {
                $role->revokePermissionTo($permission);
            }
        }

    }   

    public function deleteUser(Request $request) {

        $user = \App\User::withTrashed()->find($request->user['id']);
        if($user)   $user->removeRole();
        
    }
}
