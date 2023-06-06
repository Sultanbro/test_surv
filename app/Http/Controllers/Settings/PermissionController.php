<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use App\User;
use App\ProfileGroup;
use App\Position;
use App\Models\Books\BookGroup;
use App\Models\Videos\VideoCategory;
use App\KnowBase;
use App\Models\PermissionItem;

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
        $rolesx = Role::with('permissions')->get(['name', 'id']); 



        foreach($rolesx as $role) {
            $arr = []; 
            foreach ($role->permissions as $key => $perm) {
                $arr[$perm->name] = true;
            }

            $role->perms = $arr;
        }

   

        $items = PermissionItem::get();

        foreach ($items as $key => $item) {

            $targets = [];
            foreach ($item->targets as $key => $_target) {
                if($_target['type'] == 1) {
                    $target = User::withTrashed()->find($_target['id']);
                    $name = $target ? $target->last_name . ' ' . $target->name . ' #' . $target->id : 'Noname';
                }
                if($_target['type'] == 2) {
                    $target = ProfileGroup::find($_target['id']);
                    $name = $target ? $target->name : 'Noname';
                }
                if($_target['type'] == 3) {
                    $target = Position::find($_target['id']);
                    $name = $target ? $target->position : 'Noname';
                }
                $targets[] = [
                    'id' => $_target['id'],
                    'name' => $name,
                    'type' => $_target['type'] // user
                ];
            }

            $item->targets = $targets;
            $item->groups_all = $item->groups_all == 1;
            
            $_groups = ProfileGroup::whereIn('id', $item->groups)->get();
            $groups = [];
            foreach ($_groups as $key => $group) {
                $groups[] = [
                    'id' => $group->id,
                    'name' => $group->name,
                ];
            }
            $item->groups = $groups;

            $_roles = Role::whereIn('id', $item->roles)->get();
            $roles = [];
            foreach ($_roles as $key => $role) {
                $roles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }

            $item->roles = $roles;
        }
        
  
     

        $all_users_with_all_their_roles = User::withTrashed()->with('roles')->has('roles')->orderBy('id', 'asc')->get();

        $users = User::where(function($query) {
                    $query->whereNotNull('name')
                        ->orWhere('name', '!=', '')
                        ->orWhere('last_name', '!=', '')
                        ->orWhereNotNull('last_name');
                })
                ->whereNotIn('id', $all_users_with_all_their_roles->pluck('id')->toArray())
                ->orderBy('id', 'desc')
                ->get([\DB::raw("CONCAT(last_name,' ',name, ' #', id) as name"), 'id']);
        
        $groups_x = ProfileGroup::where('active', 1)->get(['name', 'id'])->toArray();
        array_unshift($groups_x, [
            'id' => 0, 
            'name' => 'Все отделы'
        ]);
                

        return [
            'users' => $users,
            'groups' => $groups_x,
            'roles' => $rolesx,
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
        $pages = \App\Models\Page::whereNull('parent_id')->with('children')->get();

        foreach ($pages as $key => $page) {
            $page->opened = true;
        }

        return $pages;
    }

    /**
     * Save item - not user
     * Rename updateUser to updateItem
     * @throws Exception
     */
    public function updateTarget(Request $request) {

        try {
            $item = $request->item;

            $pi = PermissionItem::find($request->item['id']);

            $be_saved = collect($item['targets']);
            $group_ids = [];
            $role_ids = [];
            $targetToArray = $be_saved->toArray();

            foreach ($item['roles'] as $role_item) $role_ids[] = $role_item['id'];
            foreach ($item['groups'] as $gr) $group_ids[] = $gr['id'];

            $exists = DB::table('permission_items')
                ->whereJsonContains('targets', $targetToArray)
                ->whereJsonContains('roles', $role_ids)
                ->whereJsonContains('groups', $group_ids)
                ->whereJsonLength('targets', '=', count($targetToArray))
                ->whereJsonLength('roles', '=', count($role_ids))
                ->whereJsonLength('groups', '=', count($group_ids))
                ->exists();

            if ($exists)
            {
                return response()->json(['error' => 'Duplicate entry for unique key.'], 400);
            }
            if ($request->item['id'] == 0) {
                $pi = PermissionItem::create([
                    'targets' => [],
                    'roles' => [],
                    'groups' => [],
                    'groups_all' => 0
                ]);
            }


            if ($pi) {
                $removed = [];
                foreach ($pi->targets as $key => $value) {
                    $it = $be_saved->where('id', $value['id'])->where('type', $value['type'])->first();
                    if ($it == null) {
                        $removed[] = $value;
                    }
                }

                foreach ($item['targets'] as $target) {

                    if ($target['type'] == 1) {
                        $user = User::withTrashed()->with('roles')->find($target['id']);

                        if ($user) {
                            $this->assignGroups($user->id, $item['groups'], $item['groups_all']);

                            $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                            $user->syncRoles($newroles);

                            $user->groups_all = $item['groups_all'] ? 1 : null;
                            $user->save();
                        }
                    }

                    if ($target['type'] == 2) {
                        $group = ProfileGroup::where('active', 1)->with('roles')->find($target['id']);

                        if ($group) {
                            $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                            $group->syncRoles($newroles);
                        }
                    }

                    if ($target['type'] == 3) {
                        $pos = Position::with('roles')->find($target['id']);

                        if ($pos) {
                            $newroles = Role::whereIn('id', $role_ids)->get()->pluck('name')->toArray();
                            $pos->syncRoles($newroles);
                        }
                    }
                }

                $pi->update([
                    'targets' => $targetToArray,
                    'roles' => $role_ids,
                    'groups' => $group_ids,
                    'groups_all' => $item['groups_all'] ? 1 : 0
                ]);


                // remove roles
                foreach ($removed as $key => $target) {

                    $item = null;
                    if ($target['type'] == 1) $item = User::withTrashed()->with('roles')->find($target['id']);
                    if ($target['type'] == 2) $item = ProfileGroup::with('roles')->find($target['id']);
                    if ($target['type'] == 3) $item = Position::with('roles')->find($target['id']);

                    if ($item) {
                        foreach ($item->roles as $key => $role) {
                            $item->removeRole($role->name);
                        }
                    }

                }
            }

            return $pi ? $pi->id : 0;

        } catch (QueryException $queryException)
        {
            if ($queryException->errorInfo[1] == 1062)
            {
                return response()->json(['error' => 'Duplicate entry for unique key.'], 400);
            }
        }
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

            if($page->children) {
                foreach ($page->children as $key => $child) {
                 
                    $permission = $child['key'] . '_view';

                    if(in_array($permission, $request->permissions)) {
                        $role->givePermissionTo($permission);       
                    } else {
                        $role->revokePermissionTo($permission);
                    }
                    
                    $permission = $child['key'] . '_edit';
                    if(in_array($permission, $request->permissions)) {
                        $role->givePermissionTo($permission);          
                    } else {
                        $role->revokePermissionTo($permission);
                    }
                }
            }

            
        }

        return $role;
    }   

    public function deleteTarget(Request $request) {
  
        $pi = PermissionItem::find($request->id);
        if($pi) {
            foreach ($pi->targets as $key => $target) {
                if($target['type'] == 1) {
                    $item = User::withTrashed()->with('roles')->find($target['id']);
                } 
                
                if($target['type'] == 2) {
                    $item = ProfileGroup::where('active',1)->with('roles')->find($target['id']);
                } 
        
                if($target['type'] == 3) {
                    $item = Position::with('roles')->find($target['id']);
                } 

                if($item) {
                    foreach ($item->roles as $key => $role) {
                        $item->removeRole($role->name);
                    }
                }
            }


            $pi->delete();
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

    /**
     * Не относится к этому контроллеру а к курсам
     */
    public function superselectAlt(Request $request)
    {
        $options = [];

        $bookgroups = BookGroup::with('books')->get();
        $playlist_cats = VideoCategory::with('playlists')->get();
        $kbs = KnowBase::whereNull('parent_id')->get();

        foreach($bookgroups as $group) {
            if(!is_null($group->books) && $group->books->count() > 0) array_push($options, [
                    'id' => $group->id,
                    'name' => $group->name,
                    'type'=> 1,
                    'disabled' => true
                ]);

            if ($group->books){
                foreach ($group->books as $book) {
                    array_push($options, [
                        'id' => $book->id,
                        'name' => $book->title,
                        'type'=> 1,
                        'disabled' => false
                    ]);
                }
            }
        }

        foreach($playlist_cats as $cat) {
            if(!is_null($cat->playlists) && $cat->playlists->count() > 0)  array_push($options, [
                    'id' => $cat->id,
                    'name' => $cat->title,
                    'type'=> 2,
                    'disabled' => true
                ]);


            if ($cat->playlists){
                foreach ($cat->playlists as $pl) {
                    array_push($options, [
                        'id' => $pl->id,
                        'name' => $pl->title,
                        'type'=> 2,
                        'disabled' => false
                    ]);
                }
            }
        }

        if ($kbs){
            foreach($kbs as $kb) {
                array_push($options, [
                    'id' => $kb->id,
                    'name' => $kb->title,
                    'type'=> 3,
                    'cat' => isset($cat->id) ? $cat->id : null,
                    'disabled' => false
                ]);
            }
        }

        return [
            'options' => $options,
        ];
    }

    
}