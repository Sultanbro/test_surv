<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use App\ProfileGroup;
use App\Position;

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

            $item['groups_all'] = $user->groups_all == 1 ? true : false;
            $item['target'] = [
                'id' => $user->id,
                'name' => $user->last_name . ' ' . $user->name . ' #' . $user->id,
                'type' => 1 // user
            ];

            $_roles = [];
            foreach ($user->roles as $key => $role) {
                $_roles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }

            $item['roles'] = $_roles;
            $item['groups'] = $this->userInGroups($user->id);
            $items[] = $item;
        }
        
        $all_groups_with_all_their_roles = ProfileGroup::where('active', 1)->with('roles')->has('roles')->orderBy('id', 'asc')->get();
        
        foreach ($all_groups_with_all_their_roles as $key => $group) {
            $item = [];

            $item['groups_all'] = false;
            $item['target'] = [
                'id' => $group->id,
                'name' => $group->name . ' #' . $group->id,
                'type' => 2 // group
            ];

            $_roles = [];
            foreach ($group->roles as $key => $role) {
                $_roles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }

            $item['roles'] = $_roles;
            $item['groups'] = [];

            $items[] = $item;
        }
        
    
        $all_positions_with_all_their_roles = Position::with('roles')->has('roles')->orderBy('id', 'asc')->get();
        
        foreach ($all_positions_with_all_their_roles as $key => $pos) {
            $item = [];

            $item['groups_all'] = false;
            $item['target'] = [
                'id' => $pos->id,
                'name' => $pos->position . ' #' . $pos->id,
                'type' => 3 // group
            ];

            $_roles = [];
            foreach ($pos->roles as $key => $role) {
                $_roles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }

            $item['roles'] = $_roles;
            $item['groups'] = [];
            
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
                ->get([\DB::raw("CONCAT(last_name,' ',name, ' #', id) as name"), 'id']);
        
        $groups = ProfileGroup::where('active', 1)->get(['name', 'id'])->toArray();
        array_unshift($groups, [
            'id' => 0, 
            'name' => 'Все отделы'
        ]);
                

        return [
            'users' => $users,
            'groups' => $groups,
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
        $pages = \App\Models\Page::get();
        return $pages;
    }

    /**
     * Save item - not user
     * Rename updateUser to updateItem
     */
    public function updateUser(Request $request) {

        $has_role_after_update = [];
        foreach ($request->items as $key => $item) {

            if($item['target']['id'] == null) continue;
            if(count($item['roles']) == 0) continue;

            if($item['target']['type'] == 1) {
                $has_role_after_update[] = $item['target']['id'];
                $user = User::withTrashed()->with('roles')->find($item['target']['id']);

                if($user) {
                    $this->assignGroups($user->id, $item['groups'], $item['groups_all']);
    
                    $role_ids = [];
                    foreach ($item['roles'] as $role_item) $role_ids[] = $role_item['id'];
    
                    $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                    
                    $user->syncRoles($newroles);
    
                    $user->groups_all = $item['groups_all'] ? 1 : null;
                    $user->save();
                    
                } 
            }
            

            if($item['target']['type'] == 2) {
                $has_role_after_update[] = $item['target']['id'];
                $group = ProfileGroup::where('active', 1)->with('roles')->find($item['target']['id']);

                if($group) {
                    //$this->assignGroups($user->id, $item['groups'], $item['groups_all']);
    
                    $role_ids = [];
                    foreach ($item['roles'] as $role_item) $role_ids[] = $role_item['id'];
    
                    $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                    $group->syncRoles($newroles);
                } 
            }
            
            if($item['target']['type'] == 3) {
                $has_role_after_update[] = $item['target']['id'];
                $pos = Position::with('roles')->find($item['target']['id']);

                if($pos) {
                    //$this->assignGroups($user->id, $item['groups'], $item['groups_all']);
    
                    $role_ids = [];
                    foreach ($item['roles'] as $role_item) $role_ids[] = $role_item['id'];
    
                    $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                    $pos->syncRoles($newroles);
                } 
            }
           
        }


        // remove roles 

        // $all_users_with_all_their_roles = User::withTrashed()->with('roles')->has('roles')->get(['id'])->pluck('id')->toArray();
 
        // $arr = array_diff($has_role_after_update, $all_users_with_all_their_roles);
        // $arr = array_values($arr);

        // $users = User::withTrashed()->whereIn('id', $arr)->with('roles')->has('roles')->get();
        // foreach ($users as $user) {
        //     foreach ($roles as $key => $role) {
        //         $user->removeRole($role->name);
        //     }
        // }
       
    }

    private function assignGroups($user_id, $items, $full_access = false)
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
            
            if($full_access || in_array($group->id, $arr)) {
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
        $item = null;

        if($request->target['type'] == 1) {
            $item = User::withTrashed()->with('roles')->find($request->target['id']);
        } 
        
        if($request->target['type'] == 2) {
            $item = ProfileGroup::where('active',1)->with('roles')->find($request->target['id']);
        } 

        if($request->target['type'] == 3) {
            $item = Position::with('roles')->find($request->target['id']);
        } 

        if($item) {
            foreach ($item->roles as $key => $role) {
                $item->removeRole($role->name);
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

    public function superselect(Request $request)
    {
        $options = [];
        $users = [];
        $groups = [];
        $positions = [];

        $_groups = ProfileGroup::whereNotNull('name')->where('active', 1)->get();
        $_positions = Position::whereNotNull('position')->get();
        $_users = User::whereNotNull('last_name')->select('id', 'name', 'last_name')->get();

        foreach ($_groups as $key => $group) {
            $groups[] = [
                'name' => $group->name,
                'id' => $group->id,
                'type' => 2
            ];
        }

        foreach ($_positions as $key => $pos) {
            $positions[] = [
                'name' => $pos->position,
                'id' => $pos->id,
                'type' => 3
            ];
        }

        foreach ($_users as $key => $user) {
            if($user->name == '' || $user->last_name == '') continue;
            $users[] = [
                'name' => $user->last_name . ' ' . $user->name,
                'id' => $user->id,
                'type' => 1
            ];
        }

        $options = array_merge($options, $users);
        $options = array_merge($options, $groups);
        $options = array_merge($options, $positions);

        return [
            'options' => $options,
        ];
    }
}
