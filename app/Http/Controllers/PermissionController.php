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


        $all_users_with_all_their_roles = User::withTrashed()->with('roles')->has('roles')->orderBy('id', 'asc')->get();
        
        
        foreach ($all_users_with_all_their_roles as $key => $user) {
            $item = [];

            $item['user_id'] = $user->id;
            $item['user'] = [
                'id' => $user->id,
                'name' => $user->last_name . ' ' . $user->name,
            ];
            foreach ($user->roles as $key => $role) {
                $item['role'] = [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }

            $item['groups'] = $this->userInGroups($user->id);
            $items[] = $item;
        }
     

        $users = User::where(function($query) {
                    $query->whereNotNull('name')
                        ->orWhere('name', '!=', '')
                        ->orWhere('last_name', '!=', '')
                        ->orWhereNotNull('last_name');
                })
                ->whereNotIn('id', $all_users_with_all_their_roles->pluck('id')->toArray())
                ->orderBy('id', 'desc')
                ->get([\DB::raw("CONCAT(last_name,' ',name) as name"), 'id']);

        return [
            'users' => $users,
            'groups' => ProfileGroup::get(['name', 'id']),
            'roles' => $roles,
            'pages' => $this->getPages(),
            'items' => $items
        ];
    }

    private function userInGroups($id) {
        $arr = [];
        $groups = ProfileGroup::get();

        foreach ($groups as $key => $group) {
            $editors_id = json_decode($group->editors_id);
            if($group->editors_id == null) continue;
            if(in_array($id, $editors_id)) {
                array_push($arr, [
                    'id' => $group->id,
                    'name' => $group->name
                ]);
            } 
        }

        return $arr;
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

        $has_role_after_update = [];
        foreach ($request->items as $key => $item) {
            if($item['user']['id'] == null) continue;
            if($item['role']['id'] == null) continue;

         
            $has_role_after_update[] = $item['user']['id'];
            $user = User::withTrashed()->with('roles')->find($item['user']['id']);
        
            if($user) {
                $this->assignGroups($user->id, $item['groups']);
                $newrole = Role::find($item['role']['id']);
         
                foreach($user->roles as $role) {
                    if(!($newrole && $role->name == $newrole->name)) {
                        $user->removeRole($role->name);
                    }
                }
                if($newrole) $user->assignRole($newrole->name);
                
            } 
        }


        // remove roles 

        $all_users_with_all_their_roles = User::withTrashed()->with('roles')->has('roles')->get(['id'])->pluck('id')->toArray();
 
        $arr = array_diff($has_role_after_update, $all_users_with_all_their_roles);
        $arr = array_values($arr);

        $users = User::withTrashed()->whereIn('id', $arr)->with('roles')->has('roles')->get();
        foreach ($users as $user) {
            foreach ($roles as $key => $role) {
                $user->removeRole($role->name);
            }
        }
       
    }

    private function assignGroups($user_id, $items)
    {
        if($items == null) $items = [];

        $arr = [];
        foreach ($items as $key => $item) {
            $arr[] = $item['id'];
        }

        $groups = ProfileGroup::get();

        foreach ($groups as $key => $group) {
            $editors_id = json_decode($group->editors_id);
            if($group->editors_id == null) $editors_id = [];

            if(in_array($group->id, $arr)) {
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

        return $role;
    }   

    public function deleteUser(Request $request) {
        $user = User::withTrashed()->with('roles')->find($request->user['id']);
        if($user) {
            foreach ($user->roles as $key => $role) {
                $user->removeRole($role->name);
            }
        }
    } 

    public function deleteRole(Request $request) {
        $role = Role::with('permissions')->find($request['role']['id']);
        if($role) {

            foreach ($role->permissions as $key => $p) {
                $role->revokePermissionTo($p->name);
            }

            $users = User::with('roles')->whereHas('roles', function ($query) use ($role) {
                $query->where('id', $role->id);
            })->get();

            foreach ($users as $key => $user) {
                $user->removeRole($role->name);
            }

            $role->delete();
        }
        
    }
}
