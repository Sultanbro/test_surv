<?php

namespace App\Http\Controllers\User;

use App\AdaptationTalk;
use App\Api\BitrixOld as Bitrix;
use App\Downloads;
use App\Events\TrackUserFiredEvent;
use App\Exports\UserExport;
use App\Facade\Referring;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetHeadToGroupRequest;
use App\KnowBase;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\Models\GroupUser;
use App\Models\User\Card;
use App\Models\User\NotificationTemplate;
use App\Models\UserRestored;
use App\Photo;
use App\Position;
use App\ProfileGroup;
use App\Service\Admin\UserService as AdminUserService;
use App\Service\Department\UserService;
use App\Service\Tenancy\CabinetService;
use App\Setting;
use App\User;
use App\UserContact;
use App\UserDeletePlan;
use App\UserDescription;
use App\UserNotification;
use App\WorkingDay;
use App\WorkingTime;
use App\Zarplata;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public AdminUserService $userService;

    public function __construct(AdminUserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth');
    }

    public function getpersons(Request $request)
    {
        $groups = ProfileGroup::query()
            ->where('active', 1)
            ->get();

        if (isset($request['filter']) && $request['filter'] == 'all') {

            $users = \DB::table('users')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                });

            if ($request['job'] != 0) {
                $users = \DB::table('users')
                    ->where('position_id', $request['job'])
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->leftJoin('bitrix_leads as bl', function ($q) {
                        // users left joint with bitrix_leads, and get last record on bitrix_leads table
                        $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                            ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                    });
            }

            if ($request['notrainees']) $users = $users->whereNot('is_trainee', $request['notrainees']);
            if ($request['start_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);

            if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
            if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);

            if ($request['segment'] != []) $users = $users->whereIn('users.segment', $request['segment']);


        } elseif (isset($request['filter']) && $request['filter'] == 'deactivated') {

            $users = \DB::table('users')
                ->whereNotNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                })
                ->where('is_trainee', 0);

            if ($request['job'] != 0) {
                $users = \DB::table('users')
                    ->where('position_id', $request['job'])
                    ->whereNotNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->leftJoin('bitrix_leads as bl', function ($q) {
                        // users left joint with bitrix_leads, and get last record on bitrix_leads table
                        $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                            ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                    })
                    ->where('is_trainee', 0);
            }

            if ($request['notrainees']) $users = $users->whereNot('is_trainee', $request['notrainees']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);
            if ($request['segment'] != []) $users = $users->whereIn('users.segment', $request['segment']);

        } elseif (isset($request['filter']) && $request['filter'] == 'nonfilled') {

            $users_1 = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->get(['users.id'])
                ->pluck('id')
                ->toArray();

            $downloads = Downloads::whereIn('user_id', array_unique($users_1))
                ->get(['user_id'])
                ->pluck('user_id')
                ->toArray();

            $users_1 = array_diff($users_1, array_unique($downloads));

            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                })
                ->where('is_trainee', 0)
                ->where(function ($query) {
                    $query->whereNull('users.position_id')
                        ->orWhereNull('users.phone')
                        ->orWhereNull('users.birthday')
                        ->orWhereNull('users.working_day_id')
                        ->orWhereNull('users.working_time_id');
                })
                ->orWhere('is_trainee', 0)
                ->whereIn('users.id', array_values($users_1));
        } elseif (isset($request['filter']) && $request['filter'] == 'trainees') {
            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                })
                ->where('is_trainee', 1)
                ->whereNull('ud.fire_date');

            if ($request['job'] != 0) {
                $users = \DB::table('users')
                    ->where('position_id', $request['job'])
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->leftJoin('bitrix_leads as bl', function ($q) {
                        // users left joint with bitrix_leads, and get last record on bitrix_leads table
                        $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                            ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                    })
                    ->where('is_trainee', 1)
                    ->whereNull('ud.fire_date');
            }

            if ($request['start_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '<=', $request['end_date']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);
        } elseif (isset($request['filter']) && $request['filter'] == 'reactivated') {
            $users = \DB::table('users')
                ->join('users_restored as ur', function ($join) {
                    $join->on('users.id', '=', 'ur.user_id')
                        ->whereRaw('ur.created_at = (SELECT MAX(created_at) FROM users_restored WHERE user_id = users.id)');
                })
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                })
                ->where('is_trainee', 0);

            if ($request['job'] != 0) {
                $users = \DB::table('users')
                    ->join('users_restored as ur', function ($join) {
                        $join->on('users.id', '=', 'ur.user_id')
                            ->whereRaw('ur.created_at = (SELECT MAX(created_at) FROM users_restored WHERE user_id = users.id)');
                    })
                    ->where('position_id', $request['job'])
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->leftJoin('bitrix_leads as bl', function ($q) {
                        // users left joint with bitrix_leads, and get last record on bitrix_leads table
                        $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                            ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                    })
                    ->where('is_trainee', 0);
            }

            if ($request['notrainees']) $users = $users->whereNot('is_trainee', $request['notrainees']);
            if ($request['start_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '<=', $request['end_date']);
            if ($request['segment']) $users = $users->whereIn('users.segment', $request['segment']);
            if ($request['start_date_deactivate']) $users = $users->whereDate('deleted_at', '>=', $request['start_date_deactivate']);
            if ($request['end_date_deactivate']) $users = $users->whereDate('deleted_at', '<=', $request['end_date_deactivate']);
            if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
            if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);

        } else {

            $users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', function ($q) {
                    // users left joint with bitrix_leads, and get last record on bitrix_leads table
                    $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                        ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                })
                ->where('is_trainee', 0);

            if ($request['job'] != 0) {
                $users = \DB::table('users')
                    ->where('position_id', $request['job'])
                    ->whereNull('deleted_at')
                    ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                    ->leftJoin('bitrix_leads as bl', function ($q) {
                        // users left joint with bitrix_leads, and get last record on bitrix_leads table
                        $q->on('bl.phone', '=', DB::raw("REGEXP_REPLACE(users.phone, '[^0-9]', '')"))
                            ->whereRaw('bl.id IN (select MAX(bl2.id) from bitrix_leads as bl2 join users as u2 on u2.phone = bl2.phone group by u2.id)');
                    })
                    ->where('is_trainee', 0);
            }
            if ($request['notrainees']) $users = $users->whereNot('is_trainee', $request['notrainees']);
            if ($request['start_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '>=', $request['start_date']);
            if ($request['end_date']) $users = $users->where(DB::raw("COALESCE(bl.created_at, users.created_at)"), '<=', $request['end_date']);
            if ($request['segment']) $users = $users->whereIn('users.segment', $request['segment']);

            if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
            if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);
        }

        $columns = [
            'users.id',
            'users.email',
            'users.user_type',
            'users.segment as segment',
            'users.last_name',
            'users.name',
            'users.full_time',
            DB::raw("CONCAT(users.last_name,' ',users.name) as FULLNAME"),
            DB::raw("CONCAT(users.name,' ',users.last_name) as FULLNAME2"),
            DB::raw("COALESCE(bl.created_at, users.created_at) as created_at"),
            'users.deleted_at',
            'users.position_id',
            'users.phone',
            'users.birthday',
            'users.description',
            'users.working_day_id',
            'users.working_time_id',
            'users.work_start',
            'users.work_end',
            'users.program_id',
            'ud.fire_cause',
            'ud.applied',
        ];
        if (isset($request['filter']) && $request['filter'] == 'reactivated') {
            if ($request['start_date_reapplied'] and $request['end_date_reapplied']) {
                $users->distinct()->join('users_restored as urs', function ($join) use ($request) {
                    $join->on('users.id', '=', 'urs.user_id')
                        ->whereBetween('urs.restored_at', [$request['start_date_reapplied'], $request['end_date_reapplied']]);
                });
                array_push($columns, 'urs.destroyed_at', 'urs.restored_at');
            } else {
                array_push($columns, 'ur.destroyed_at', 'ur.restored_at');
            }
        }
        if ($request['start_date_reapplied'] and $request['end_date_reapplied'] and $request['filter'] != 'reactivated') {
            $users->distinct()->join('users_restored as urst', function ($join) use ($request) {
                $join->on('users.id', '=', 'urst.user_id')
                    ->whereBetween('urst.restored_at', [$request['start_date_reapplied'], $request['end_date_reapplied']]);
            });
            array_push($columns, 'urst.destroyed_at', 'urst.restored_at');
        }
        $users = $users->get($columns);


        foreach ($users as $key => $user) {

            $_user = User::withTrashed()->find($user->id);

            $userGroups = collect($this->getPersonGroup($_user->id))->pluck('id')->toArray();
            $user->groups = $_user ? $userGroups : [];

            if (is_null($user->deleted_at) || $user->deleted_at == '0000-00-00 00:00:00') {
                $user->deleted_at = '';
            } else {
                $user->deleted_at = Carbon::parse($user->deleted_at)->addHours(6)->format('Y-m-d H:i:s');
                if ($user->deleted_at == '30.11.-0001 00:00:00') {
                    $user->deleted_at = '';
                }
            }


//            if ($request['start_date_applied'] != null &&
//                Carbon::parse($user->applied)->timestamp - Carbon::parse($request['start_date_applied'])->timestamp < 0) {
//                $users->forget($key);
//                continue;
//            }
//
//            if ($request['end_date_applied'] != null &&
//                Carbon::parse($user->applied)->timestamp - Carbon::parse($request['end_date_applied'])->timestamp > 0) {
//                $users->forget($key);
//                continue;
//            }

            $user->created_at = Carbon::parse($user->created_at)->addHours(6)->format('Y-m-d H:i:s');

            if ($user->applied) {
                $user->applied = Carbon::parse($user->applied)->addHours(6)->format('Y-m-d H:i:s');
            }


            if (isset($request['filter']) && $request['filter'] == 'deactivated') {
                $deleted = GroupUser::where('status', 'fired')
                    ->where('user_id', $user->id)
                    ->get()
                    ->pluck('group_id')
                    ->toArray();

                $user->groups = $deleted;
            } elseif ($user->deleted_at) {
                $deleted = GroupUser::where('status', 'fired')
                    ->where('user_id', $user->id)
                    ->get()
                    ->pluck('group_id')
                    ->toArray();

                $user->groups = $deleted;
            }

        }


        ////////////////////////

        $groups = $groups->pluck('name', 'id')->toArray();

        if ($request->excel) {
            $export = new UserExport($users, $groups);
            $title = 'Сотрудники: ' . date('Y-m-d') . '.xlsx';
            return Excel::download($export, $title);
        }


        $users = $users->values();


        ////////////////////

        return [
            'users' => $users,
            'can_login_users' => [5, 18, 1],
            'auth_token' => Auth::user()->remember_token,
            'currentUser' => Auth::user()->id,
            'segments' => Segment::query()->get(['id', 'name', 'active']),
            'groups' => [0 => 'Выберите отдел'] + $groups,
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function newGetPersons(Request $request)
    {
        $groups = ProfileGroup::query()
            ->where('active', 1)
            ->get();

        if (isset($request['filter']) && $request['filter'] == 'all') {
            if ($request['job'] != 0) {
                $users = User::withTrashed()
                    ->where('position_id', $request['job']);
            } else {
                $users = User::withTrashed();
            }
            $users = $users
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->with(['group_users']);

            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('group_id', $request['group_id']);
            });
        }
        elseif (isset($request['filter']) && $request['filter'] == 'deactivated') {
            if ($request['job'] != 0) {
                $users = User::withTrashed()
                    ->where('position_id', $request['job']);
            } else {
                $users = User::withTrashed();
            }
            $users = $users
                ->whereNotNull('users.deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->where('is_trainee', 0)
                ->with(['group_users' => function ($query) {
                    $query->where('status', 'fired');
                }]);

            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('status', 'fired')->where('group_id', $request['group_id']);
            });
        }
        elseif (isset($request['filter']) && $request['filter'] == 'nonfilled') {

            $users_1 = User::query()
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->get(['users.id'])
                ->pluck('id')
                ->toArray();

            $downloads = Downloads::whereIn('user_id', array_unique($users_1))
                ->get(['user_id'])
                ->pluck('user_id')
                ->toArray();

            $users_1 = array_diff($users_1, array_unique($downloads));

            $users = User::query()
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->where('is_trainee', 0)
                ->where(function ($query) {
                    $query->whereNull('users.position_id')
                        ->orWhereNull('users.phone')
                        ->orWhereNull('users.birthday')
                        ->orWhereNull('users.working_day_id')
                        ->orWhereNull('users.working_time_id');
                })
                ->orWhere('is_trainee', 0)
                ->whereIn('users.id', array_values($users_1))
                ->with(['group_users' => function ($query) {
                    $query->where('status', 'active');
                }]);
            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('status', 'active')->where('group_id', $request['group_id']);
            });
        }
        elseif (isset($request['filter']) && $request['filter'] == 'trainees') {
            if ($request['job'] != 0) {
                $users = User::query()
                    ->where('position_id', $request['job']);
            } else {
                $users = User::query();
            }
            $users = $users
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->where('is_trainee', 1)
                ->whereNull('ud.fire_date')
                ->with(['group_users' => function ($query) {
                    $query->where('status', 'active');
                }]);

            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('status', 'active')->where('group_id', $request['group_id']);
            });
        }
        elseif (isset($request['filter']) && $request['filter'] == 'reactivated') {
            if ($request['job'] != 0) {
                $users = User::withTrashed()
                    ->where('position_id', $request['job']);
            } else {
                $users = User::withTrashed();
            }
            $users = $users
                ->join('users_restored as ur', function ($join) {
                    $join->on('users.id', '=', 'ur.user_id')
                        ->whereRaw('ur.created_at = (SELECT MAX(created_at) FROM users_restored WHERE user_id = users.id)');
                })
                ->whereNull('users.deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->where('is_trainee', 0)
                ->with(['group_users' => function ($query) {
                    $query->where('status', 'active');
                }]);

            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('status', 'active')->where('group_id', $request['group_id']);
            });
        }
        else {
            if ($request['job'] != 0) {
                $users = User::query()
                    ->where('position_id', $request['job']);
            } else {
                $users = User::query();
            }
            $users = $users
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->leftJoin('bitrix_leads as bl', 'users.id', '=', 'bl.user_id')
                ->leftJoin('position', 'users.position_id', '=', 'position.id')
                ->where('is_trainee', 0)
                ->with(['group_users' => function ($query) {
                    $query->where('status', 'active');
                }]);

            if ($request['group_id']) $users = $users->whereHas('group_users', function($q) use ($request) {
                $q->where('status', 'active')->where('group_id', $request['group_id']);
            });
        }

        if ($request['notrainees']) $users = $users->whereNot('is_trainee', $request['notrainees']);
        if ($request['start_date']) $users = $users->where(DB::raw("date(COALESCE(bl.skyped, users.created_at))"), '>=', $request['start_date']);
        if ($request['end_date']) $users = $users->where(DB::raw("date(COALESCE(bl.skyped, users.created_at))"), '<=', $request['end_date']);
        if ($request['start_date_deactivate']) $users = $users->whereDate('users.deleted_at', '>=', $request['start_date_deactivate']);
        if ($request['end_date_deactivate']) $users = $users->whereDate('users.deleted_at', '<=', $request['end_date_deactivate']);
        if ($request['start_date_applied']) $users = $users->whereDate('applied', '>=', $request['start_date_applied']);
        if ($request['end_date_applied']) $users = $users->whereDate('applied', '<=', $request['end_date_applied']);
        if ($request['segment']) $users = $users->whereIn('users.segment', $request['segment']);
        if ($request['type']) $users = $users->where('user_type', $request['type']);
        if ($request['part'] && $request['part'] == 'full') $users = $users->where('full_time', 1);
        if ($request['part'] && $request['part'] == 'part') $users = $users->where('full_time', 0);

        if ($request['search']) {
            $users = $users
                ->where(function ($query) use ($request){
                    $query->where('users.email', 'like', $request['search'] . '%')
                        ->orWhere('users.id', $request['search'])
                        ->orWhere(DB::raw("CONCAT(users.last_name,' ',users.name)"), 'like', $request['search'] . '%')
                        ->orWhere(DB::raw("CONCAT(users.name,' ',users.last_name)"), 'like', $request['search'] . '%')
                        ->orWhere('users.name', 'like', $request['search'] . '%')
                        ->orWhere('users.last_name', 'like', $request['search'] . '%')
                        ->orWhere('working_country', 'like', '%' . $request['search'] . '%');
                });

        }

        $columns = [
            'users.id',
            'users.email',
            'users.user_type',
            'users.segment as segment',
            'users.last_name',
            'users.name',
            'users.full_time',
            'users.working_country',
            DB::raw("CONCAT(users.last_name,' ',users.name) as FULLNAME"),
            DB::raw("CONCAT(users.name,' ',users.last_name) as FULLNAME2"),
            DB::raw("COALESCE(bl.skyped, users.created_at) as created_at"),
            'users.deleted_at',
            'users.position_id',
            'users.phone',
            'ud.fire_cause',
            'ud.applied',
            'position.position'
        ];
        if (isset($request['filter']) && $request['filter'] == 'reactivated') {
            if ($request['start_date_reapplied'] and $request['end_date_reapplied']) {
                $users->distinct()->join('users_restored as urs', function ($join) use ($request) {
                    $join->on('users.id', '=', 'urs.user_id')
                        ->whereBetween('urs.restored_at', [$request['start_date_reapplied'], $request['end_date_reapplied']]);
                });
                array_push($columns, 'urs.destroyed_at', 'urs.restored_at');
            } else {
                array_push($columns, 'ur.destroyed_at', 'ur.restored_at');
            }
        }
        if ($request['start_date_reapplied'] and $request['end_date_reapplied'] and $request['filter'] != 'reactivated') {
            $users->distinct()->join('users_restored as urst', function ($join) use ($request) {
                $join->on('users.id', '=', 'urst.user_id')
                    ->whereBetween('urst.restored_at', [$request['start_date_reapplied'], $request['end_date_reapplied']]);
            });
            array_push($columns, 'urst.destroyed_at', 'urst.restored_at');
        }

        $part_time = clone $users;
        $full_time = clone $users;
        $part_time_count = $part_time->distinct('users.id')->where('full_time', 0)->count();
        $full_time_count = $full_time->distinct('users.id')->where('full_time', 1)->count();
        $users = $users->select($columns);

        //////
        ///
        /// Sort by column and direction
        ///
        //////

        $sortDirection = 'asc';
        if ($request['sortDirection'] && $request['sortDirection'] == 'desc') $sortDirection = 'desc';

        if ($request['sortBy'] && in_array($request['sortBy'], [
                'name', 'last_name', 'group', 'created_at', 'users.deleted_at',
                'fire_cause', 'user_type', 'segment', 'applied', 'full_time', 'position'
            ])
        ) {
            $users = $users->orderBy($request['sortBy'], $sortDirection);
        }

        $users = $users->paginate($request['perPage'] ?? 20);

        foreach ($users as $key => $user) {
            if (isset($request['filter']) && $request['filter'] == 'all') {
                $status = 'active';
                if ($user->deleted_at) {
                    $status = 'fired';
                }
                $new_groups = array_unique($user->group_users->where('status', $status)->pluck('group_id')->toArray(), SORT_NUMERIC);
            } else {
                $new_groups = array_unique($user->group_users->pluck('group_id')->toArray(), SORT_NUMERIC);
            }

            if ($user->relationLoaded('group_users')) {
                unset($user->group_users);
            }
            sort($new_groups);
            $users[$key]['groups'] = $new_groups;

            if (is_null($user->deleted_at) || $user->deleted_at == '0000-00-00 00:00:00') {
                $user->deleted_at = null;
            } else {
                $user->deleted_at = $user->deleted_at->addHours(6)->format('Y-m-d H:i:s');
                if ($user->deleted_at == '30.11.-0001 00:00:00') {
                    $user->deleted_at = null;
                }
            }

            if (!is_null($user->created_at)) {
                $user->created_at = Carbon::parse($user->created_at)->addHours(6)->format('Y-m-d H:i:s');
            }

            if ($user->applied) {
                $user->applied = Carbon::parse($user->applied)->addHours(6)->format('Y-m-d H:i:s');
            }
        }


        ////////////////////////

        $groups = $groups->pluck('name', 'id')->toArray();

        if ($request->excel) {
            $export = new UserExport($users, $groups);
            $title = 'Сотрудники: ' . date('Y-m-d') . '.xlsx';
            return Excel::download($export, $title);
        }


        ////////////////////

        return [
            'users' => $users,
            'users_part_time' => $part_time_count,
            'users_full_time' => $full_time_count,
            'can_login_users' => [5, 18, 1],
            'auth_token' => Auth::user()->remember_token,
            'currentUser' => Auth::user()->id,
            'segments' => Segment::query()->get(['id', 'name', 'active']),
            'groups' => [0 => 'Выберите отдел'] + $groups,
            'start_date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function getPerson(Request $request){

        $user = User::withTrashed()
            ->where('id', $request->id)
            ->first();

        return [
            'user' => $user,
        ];
    }

    /**
     * createPerson
     */
    public function createPerson()
    {
        // $user = Auth::user();
        // $rectuiting = ProfileGroup::find(48);
        // if($rectuiting) $users = json_decode($rectuiting->users);
        // if(in_array($user, ))

        if (!Auth::user()) return redirect('/');

        View::share('title', 'Новый сотрудник');
        View::share('menu', 'timetrackingusercreate');

        if (!auth()->user()->can('users_view')) {
            return redirect('/');
        }

        return view('admin.users.create', $this->preparePersonInputs());

    }

    /**
     * editPerson
     *
     * @param Request $request
     * @param null $type
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editPerson(Request $request, $type = null)
    {
        if (!Auth::user()) return redirect('/');

        View::share('title', 'Редактировать сотрудника');
        View::share('menu', 'timetrackingusercreate');

        if (!auth()->user()->can('users_view')) {
            return redirect('/');
        }
        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    /**
     * prepare user variables to settings page
     * @throws Exception
     */
    private function preparePersonInputs($id = 0)
    {
        $positions = Position::all();
        $groups = ProfileGroup::where('active', 1)->get();
        $corpbooks = [];

        $knowbase_models = DB::table('knowbase_model')
            ->where('model_id', auth()->id() ?? 5)
            ->get()
            ->toArray();

        if (!empty($knowbase_models)) {
            foreach ($knowbase_models as $k => $knowbase_model) {
                $knowbase_query[] = KnowBase::where('id', $knowbase_model->book_id)->get()->toArray();
            }

            foreach ($knowbase_query as $corpbook) {
                $corpbooks[] = array_shift($corpbook);
            }
        }

        $programs = \App\Models\Program::orderBy('id', 'desc')->get();
        $workingDays = WorkingDay::all();
        $workingTimes = WorkingTime::all();
        $timezones = Setting::TIMEZONES;

        $arr = compact('positions', 'groups', 'timezones', 'programs', 'workingDays', 'workingTimes', 'corpbooks');

        if ($id != 0) {
            $user = User::withTrashed()
                ->where('id', $id)
                ->with(['zarplata', 'downloads', 'user_description', 'coordinate', 'restoredData'])
                ->first();

            if ($user->weekdays == '' || $user->weekdays == null) {
                $user->weekdays = '0000000';
                $user->save();
            }

//            $currency = !in_array($user->currency, ['kzt', 'rub', 'usd']) ? 'usd' : $user->currency;
//            $user->zarplata->zarplata = round(CurrencyTrait::createMultiCurrencyPrice($user->zarplata->zarplata)[$currency], 2);

            $user->cards = Card::where('user_id', $user->id)->get();

            $user->delete_time = null;
            $head_in_groups = [];

            if ($user) {
                if ($user->trainee) {
                    $user->applied_at = $user->applied;
                } else {
                    $user->applied_at = $user->created_at;
                }

                if ($user->is_trainee) {
                    $arr['fire_causes'] = [
                        'Был на основной работе',
                        'Бросает трубку',
                        'Вышел (-ла) из группы',
                        'Забыл (-а), после обеда присутствует',
                        'Нашел(-а) другую работу',
                        'Не был на обучении / стажировке',
                        'Не выходит на связь',
                        'Не понравились условия оплаты труда',
                        'Не сдал экзамен',
                        'Не смог подключиться',
                        'Не хочет долго стажироваться',
                        'Не хочет работать 6 дней',
                        'Отказ от стажировки',
                        'Отсутствовал(а) более 3 дней',
                        'По техническим причинам',
                        'Пропал с обучения',
                        'Ребенок заболел, не сможет совмещать',
                        'Удалился (-ась), не актуально',
                    ];
                } else {
                    $arr['fire_causes'] = [
                        'Взял перерыв, позже возможно будет работать',
                        'Дисциплинарные нарушения',
                        'Дубликат, 2 учетки',
                        'Заказчик снял с проекта',
                        'Игнорирование предупреждений',
                        'Не справился с обязанностями',
                        'Конфликт с коллегами',
                        'Нашел(-а) другую работу',
                        'Неадекватная личность',
                        'Некому за ребенком присматривать',
                        'Не выходит на связь более 7 дней',
                        'Не успевает по учебе',
                        'Не устраивает график',
                        'Не устраивает ЗП',
                        'Не устраивает пункт в договоре',
                        'Оказалось что есть вторая работа',
                        'Переезд в другой город',
                        'Плохие рабочие показатели/не справился',
                        'По семейным обстоятельствам',
                        'По состоянию здоровья',
                        'По техническим причинам',
                        'Проект закрыт. Снят с линии',
                        'Решил(-а) работать оффлайн',
                        'Слишком большая нагрузка',
                    ];
                }


                $groups = $user->inGroups(true);

                foreach ($groups as $gr) {
                    array_push($head_in_groups, $gr);
                }

                $delete_plan = UserDeletePlan::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if ($delete_plan) $user->delete_time = $delete_plan->delete_time;


                if ($user->user_description) {
                    $user->fire_cause = $user->user_description->fire_cause;
                    $user->recruiter_comment = $user->user_description->recruiter_comment;
                    $user->bitrix_id = $user->user_description->bitrix_id;
                }

                $lead = Lead::where('user_id', $user->id)->first();
                $user->lead = $lead ? $lead : null;

                $seg = Segment::find($user->segment);
                $segment = $seg ? $seg->name : '';

                if ($segment != '') {
                    $user->segment = $segment;
                }

                if ($user->deleted_at != null && $user->deleted_at != '0000-00-00 00:00:00') {
                    $user->worked_with_us = round((Carbon::parse($user->deleted_at)->timestamp - Carbon::parse($user->applied_at)->timestamp) / 3600 / 24) . ' дней';
                    $user->in_groups = $this->getLastFiredGroupForFiredEmployee($user->id);
                } else if (!$user->is_trainee && $user->deleted_at == null) {
                    $user->worked_with_us = round((Carbon::now()->timestamp - Carbon::parse($user->created_at)->timestamp) / 3600 / 24) . ' дней';
                    $user->in_groups = $this->getPersonGroup($user->id);
                } else {
                    $user->worked_with_us = 'Еще стажируется';
                    $user->in_groups = $this->getPersonGroup($user->id);
                }

                if ($user->user_description) {
                    $user->in_books = '[]';
                }

                $user->head_in_groups = $head_in_groups;
            }


            $user->adaptation_talks = AdaptationTalk::getTalks($user->id);

            $arr['user'] = $user;
        }


        return $arr;
    }

    /**
     * Update user profile from settings
     *
     * @param Request $request
     * @throws \Exception
     */
    public function updatePerson(Request $request)
    {
        if (!auth()->user()->can('users_view')) {
            return redirect('/');
        }
        /*==============================================================*/
        /********** Подготовка  */
        /********** Есть момент, что можно посмотреть любого пользователя (не сотрудника ), не знаю баг или нет  */
        /*==============================================================*/

        $id = $request['id'];
        $user = User::with('zarplata')->where('id', $id)->withTrashed()->first();
        $photo = Photo::where('user_id', $id)->first();
        $downloads = Downloads::where('user_id', $id)->first();

        $zarplata = !is_null($user->zarplata) && !is_null($user->zarplata->zarplata) ? $user->zarplata->zarplata : 0;


        /*==============================================================*/
        /********** Проверка новой почты существует ли  */
        /*==============================================================*/
        $oldUser = User::withTrashed()->where('email', $request['email'])->first();

        if ($oldUser && $request['email'] != $user->email) { // Существует

            if ($oldUser->deleted_at != null) {  // Ранее уволен
                $text = '<p>Нужно ввести другую почту, так как сотрудник c таким email ранее был уволен:</p>';
                $text .= '<table class="table" style="border-collapse: separate; margin-bottom: 15px;">';
                $text .= '<tr><td><b>Имя:</b></td><td>' . $oldUser->name . '</td></tr>';
                $text .= '<tr><td><b>Фамилия:</b></td><td>' . $oldUser->last_name . '</td></tr>';
                $text .= '<tr><td><b>Email:</b></td><td><a href="/timetracking/edit-person?id=' . $oldUser->id . '" target="_blank"> ' . $oldUser->email . '</a></td></tr>';
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>' . Carbon::parse($oldUser->deleted_at)->setTimezone('Asia/Dacca') . '</td></tr>';
                $text .= '</table>';
                return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);
            }


            $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $request['email'] . '<br><a href="/timetracking/edit-person?id=' . $oldUser->id . '"   target="_blank">' . $oldUser->last_name . ' ' . $oldUser->name . '</a>';
            return redirect()->to('/timetracking/edit-person?id=' . $request['id'])->withInput()->withErrors($text);


        }

        /*==============================================================*/
        /********** Редактирование user  */
        /*==============================================================*/

        if (isset($request['selectedCityInput']) && !empty($request['selectedCityInput'])) {

            if (auth()->user()->working_city === $request['working_city']) {
                $country = $request['selectedCityInput'];
                $explodeCountry = explode(' ', $country);
                foreach ($explodeCountry as $country) {
                    $searchCountry = DB::table('coordinates')->where('city', $country)->get()->toArray();
                }
                if (isset($searchCountry[0]->id) && !empty($searchCountry)) {
                    $request['working_city'] = $searchCountry[0]->id;
                } else {
                    $request['working_city'] = null;
                    $request['selectedCityInput'] = null;
                }
            }
        } else {
            $request['working_city'] = null;
            $request['selectedCityInput'] = null;
        }

        if ($request['is_trainee'] == "false") {
            $user->description()->update([
                'is_trainee' => 0
            ]);
        }

        $user->email = strtolower($request['email']);
        $user->name = $request['name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        $user->phone_1 = $request['phone_1'];
        $user->phone_2 = $request['phone_2'];
        $user->phone_3 = $request['phone_3'];
        $user->phone_4 = $request['phone_4'];
        $user->birthday = $request['birthday'];
        $user->full_time = $request['full_time'];
        $user->description = $request['description'];
        $user->currency = $request['currency'] ?? 'kzt';
        $user->position_id = $request['position'] ?? 0;
        $user->user_type = $request['user_type'];
        $user->timezone = 6;
        $user->program_id = (int)$request['program_type'];
        $user->working_day_id = (int)$request['working_days'];
        $user->working_time_id = (int)$request['working_times'];
        $user->work_start = $request['work_start_time'];
        $user->work_end = $request['work_start_end'];
        $user->weekdays = $request['weekdays'];
        $user->working_country = $request['selectedCityInput'];
        $user->working_city = $request['working_city'];
        $user->headphones_sum = $request['headphones_amount'];


        if ($request->new_pwd != '') {
            $user->password = \Hash::make($request->new_pwd);
        }

        $user->save();


        /**
         * Adaptation talks
         */
        foreach ($request->adaptation_talks as $key => $talk) {

            $at = AdaptationTalk::where('user_id', $user->id)->where('day', $talk['day'])->first();
            if ($at) {
                $at->inter_id = $talk['inter_id'];
                $at->text = $talk['text'];
                $at->date = $talk['date'];
                $at->save();
            } else {
                AdaptationTalk::create([
                    'inter_id' => $talk['inter_id'],
                    'text' => $talk['text'],
                    'day' => $talk['day'],
                    'date' => $talk['date'],
                    'user_id' => $user->id,
                ]);
            }
        }
        /**
         * Сохранение налоговых начислений.
         */
        // try {
        //     (new TaxService)->userTax($request);
        // } catch (\Exception $e) {
        //     throw new \Exception($e->getMessage());
        // }

        /**
         *  Битрикс ID профиля
         */

        $ud = UserDescription::where('user_id', $user->id)
            ->first();

        if ($ud) {
            $ud->bitrix_id = $request->bitrix_id;

            // Headphones for Salary
            if ($request->headphones_amount > 0) {
                $ud->headphones_amount = $request->headphones_amount;
                $ud->headphones_date = date('Y-m-d');
            } else {
                $ud->headphones_amount = 0;
                $ud->headphones_date = null;
            }

            $ud->save();
        }

        if (in_array($user->segment, [7, 8, 9, 10, 11, 12])) {

            $ud = UserDescription::where('is_trainee', 0)->where('user_id', $user->id)->first();

            if ($ud) {
                $comment = '';
                if ($request->recruiter_comment != '') {
                    $ud->recruiter_comment = $request->recruiter_comment;
                    $ud->save();
                    $comment = $request->recruiter_comment;
                }

                $seg = Segment::find($user->segment);
                $segment = $seg ? $seg->name : '';

                $msg_fragment = '<a href="https://' . tenant('id') . '.jobtron.org/timetracking/edit-person?id=';
                $msg_fragment .= $user->id . '">' . $user->last_name . ' ' . $user->name . '</a>';
                $msg_fragment .= '<br/>Дата принятия: ' . Carbon::parse($ud->applied)->format('d.m.Y');
                $msg_fragment .= '<br/>Сегмент: ' . $segment . '<br/>Примечание: ' . $comment;

                $timestamp = now();
                $notification_receivers = NotificationTemplate::getReceivers(10);

                foreach ($notification_receivers as $user_id) {
                    UserNotification::create([
                        'user_id' => $user_id,
                        'about_id' => 0,
                        'title' => 'Оплатите внешнему рекрутеру за нового сотрудника',
                        'group' => $timestamp,
                        'message' => $msg_fragment
                    ]);

                }
            }


        }

        /*==============================================================*/
        /*******  Руковод или нет  */
        /*==============================================================*/
        if ($request->position == 45) {

            $last_groups = $user->headInGroups();
            foreach ($last_groups as $gr) {

                $gr_users = json_decode($gr->head_id);
                $gr_users = array_diff($gr_users, [$user->id]);
                $gr_users = array_values($gr_users);
                $gr->head_id = json_encode($gr_users);
                $gr->save();

            }
        } else {
            $user->groups()->where('status', 'active')->update([
                'is_head' => 0
            ]);
        }
        /*==============================================================*/
        /********** Добавление дополнительных телефонов  */
        /*==============================================================*/

        if ($request->has('contacts') && isset($request->contacts['phone'])) {
            $user->profileContacts()->delete(); // Удаляет что было
            foreach ($request->contacts['phone'] as $phone) {
                $user->profileContacts()->save(
                    UserContact::create([
                        'user_id' => 0,
                        'type' => 'phone',
                        'name' => $phone['name'],
                        'value' => $phone['value'],
                    ])
                );
            }
        }

        /*==============================================================*/
        /********** Добавление дополнительных карт  */
        /*==============================================================*/

        if ($request->has('cards') && count($request->cards) > 0) {
            $cards = Card::where('user_id', $user->id)->delete();
            foreach ($request->cards as $card) {
                Card::create([
                    'user_id' => $user->id,
                    'bank' => $card['bank'],
                    'country' => $card['country'],
                    'cardholder' => $card['cardholder'],
                    'phone' => $card['phone'],
                    'number' => $card['number'],
                ]);
            }
        }


        /*==============================================================*/
        /********** Документы пользователя  */
        /*==============================================================*/
        if ($request->hasFile('file1')) {
            $file = $request->file('file1');
            $dog_okaz_usl = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_okaz_usl", $dog_okaz_usl);
        }
        if ($request->hasFile('file2')) {
            $file = $request->file('file2');
            $sohr_kom_tainy = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/sohr_kom_tainy", $sohr_kom_tainy);
        }
        if ($request->hasFile('file3')) {
            $file = $request->file('file3');
            $dog_o_nekonk = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/dog_o_nekonk", $dog_o_nekonk);
        }
        if ($request->hasFile('file4')) {
            $file = $request->file('file4');
            $trud_dog = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/trud_dog", $trud_dog);
        }
        if ($request->hasFile('file5')) {
            $file = $request->file('file5');
            $ud_lich = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/ud_lich", $ud_lich);
        }
        if ($request->hasFile('file7')) {
            $file = $request->file('file7');
            $archive = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/archive", $archive);
        }

        if ($request->hasFile('file6')) {
            $file = $request->file('file6');
            $photo_file = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move("static/profiles/" . $user->id . "/photo", $photo_file);
            if (is_null($photo)) {
                $photo = Photo::create([
                    'user_id' => $id,
                    'path' => isset($photo_file) ? $photo_file : null,
                ]);
            } else {
                if (isset($photo_file)) {
                    $photo->path = $photo_file;
                    $photo->save();
                }
            }

        }

        if ($downloads) {
            if (isset($ud_lich)) $downloads->ud_lich = $ud_lich;
            if (isset($dog_okaz_usl)) $downloads->dog_okaz_usl = $dog_okaz_usl;
            if (isset($sohr_kom_tainy)) $downloads->sohr_kom_tainy = $sohr_kom_tainy;
            if (isset($dog_o_nekonk)) $downloads->dog_o_nekonk = $dog_o_nekonk;
            if (isset($trud_dog)) $downloads->trud_dog = $trud_dog;
            if (isset($archive)) $downloads->archive = $archive;
            $downloads->save();
        } else {
            $downloads = Downloads::create([
                'user_id' => $id,
                'ud_lich' => isset($ud_lich) ? $ud_lich : null,
                'dog_okaz_usl' => isset($dog_okaz_usl) ? $dog_okaz_usl : null,
                'sohr_kom_tainy' => isset($sohr_kom_tainy) ? $sohr_kom_tainy : null,
                'dog_o_nekonk' => isset($dog_o_nekonk) ? $dog_o_nekonk : null,
                'trud_dog' => isset($trud_dog) ? $trud_dog : null,
                'archive' => isset($archive) ? $archive : null,
            ]);
        }

        /*==============================================================*/
        /********** Редактирование зарплаты */
        /*==============================================================*/

        if ($user->zarplata === null) {
            $zarplata = new Zarplata();
            $zarplata->user_id = $user->id;
            $zarplata->zarplata = $request->zarplata == 0 ? 70000 : $request->zarplata;
            $zarplata->kaspi = $request->kaspi;
            $zarplata->jysan = $request->jysan;
            $zarplata->card_kaspi = $request->card_kaspi;
            $zarplata->kaspi_cardholder = $request->kaspi_cardholder;
            $zarplata->card_jysan = $request->card_jysan;
            $zarplata->jysan_cardholder = $request->jysan_cardholder;
            $zarplata->card_number = intval(preg_replace('/\s+/', '', $request->card_number));
            $zarplata->save();
        } else {


            $user->zarplata()->update([
                'zarplata' => $request->zarplata,
                'card_number' => $request->card_number,
                'kaspi' => $request->kaspi,
                'jysan' => $request->jysan,
                'card_kaspi' => $request->card_kaspi,
                'card_jysan' => $request->card_jysan,
                'kaspi_cardholder' => $request->kaspi_cardholder,
                'jysan_cardholder' => $request->jysan_cardholder,
            ]);
        }

        // Test
        $_groups = [];

        $groups = ProfileGroup::where('active', 1)->get();

        foreach ($groups as $group) {
            if ($group->users == null) continue;
            $group_users = json_decode($group->users);

            if (in_array($user->id, $group_users)) {
                $group->show = false;
                array_push($_groups, $group->id);
            }
        }

        if ($request->increment_provided == 'true' && count($_groups) > 0) {

            $group = ProfileGroup::find($_groups[0]);
            if ($group) {
                $group->provided = $group->provided + 1;
                /*******  Увеличиваем принятых в отдел */
                $group->save();
            }

        }

        (new CabinetService)->remove(tenant('id'), $user);

        return redirect()->to('/timetracking/edit-person?id=' . $user->id);
    }

    /**
     * edit user groups in settings
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function editPersonGroup(Request $request)
    {

        (new UserService)->setGroup(
            $request['group_id'],
            $request['user_id'],
            $request['action']
        );
    }

    /**
     * Назначить руководителя группы.
     *
     * @param SetHeadToGroupRequest $request
     * @return void
     * @throws Exception
     */
    public function setUserHeadInGroups(SetHeadToGroupRequest $request): void
    {
        $group = ProfileGroup::find($request['group_id']);
        $exist = $group->users()->where([
            ['user_id', $request['user_id']],
            ['status', 'active'],
            ['is_head', true]
        ])->whereNull('to')->exists();

        try {
            if ($request['action'] == 'add' && !$exist) {
                $group->users()->where('user_id', $request['user_id'])->update([
                    'is_head' => true
                ]);
            }

            if ($request['action'] == 'delete') {
                $group->users()->where('user_id', $request['user_id'])->update([
                    'is_head' => false
                ]);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }
    }

    /**
     * get user groups
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getPersonGroup(int $user_id): array
    {
        $groups = GroupUser::where('user_id', $user_id)
            ->where('status', 'active')
            ->get()
            ->pluck('group_id')
            ->toArray();

        return ProfileGroup::whereIn('id', array_values($groups))
            ->select(['id', 'name'])
            ->get()
            ->toArray();
    }

    public function getLastFiredGroupForFiredEmployee(int $user_id)
    {
        $group = GroupUser::query()->where('user_id', $user_id)->where('status', 'fired')->latest()->first();
        return ProfileGroup::query()->where('id', $group->group_id)->get(['id', 'name']);
    }

    /**
     * Fire user
     */
    public function deleteUser(Request $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::query()->where([
                'id' => $request->id,
            ])->first();

            event(new TrackUserFiredEvent($user));

            // Есть заявление об увольнении
            if ($request->hasFile('file8')) { // Заявление об увольнении
                $file = $request->file('file8');
                $resignation = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                File::ensureDirectoryExists("static/profiles/" . $user->id . "/resignation");
                $file->move("static/profiles/" . $user->id . "/resignation", $resignation);
                $downloads = Downloads::where('user_id', $user->id)->first();
                if ($downloads) {
                    $downloads->resignation = $resignation;
                    $downloads->save();
                } else {
                    Downloads::query()->create([
                        'user_id' => $user->id,
                        'ud_lich' => null,
                        'dog_okaz_usl' => null,
                        'sohr_kom_tainy' => null,
                        'dog_o_nekonk' => null,
                        'trud_dog' => null,
                        'archive' => null,
                        'resignation' => $resignation,
                    ]);
                }
            }
            // Причина увольенения
            $cause = $request->cause2 == '' ? $request->cause : $request->cause2;
            $fire_date = now();

            UserDescription::query()->updateOrCreate([
                'user_id' => $request->id
            ], [
                'fire_cause' => $cause,
                'fire_date' => $fire_date
            ]);


            ///////  УВолить с отработкой или без

            if ($request->delay == 1) { // Удалить через 2 недели


                $fire_date = Carbon::now()->addHours(24 * 14);

                UserDeletePlan::query()->updateOrCreate([
                    'user_id' => $user->id
                ], [
                    'executed' => 0,
                    'delete_time' => $fire_date,
                ]);

            } else { // Сразу удалить


                /////////// Удалить связанные уведомления
                $notis = UserNotification::where('about_id', $user->id)->get();
                if ($notis->count() > 0) {
                    foreach ($notis as $noti) {
                        $noti->read_at = now();
                        $noti->save();
                    }
                }

                //////////////////////////////

                $trainee = UserDescription::where('is_trainee', 1)->where('user_id', $request->id)->first();

                if ($trainee) {
                    if ($trainee->lead_id != 0 && $trainee->lead_id) {
                        $lead = Lead::where('lead_id', $trainee->lead_id)->orderBy('id', 'desc')->first();
                    } else {
                        $lead = Lead::where('phone', $user->phone)->orderBy('id', 'desc')->first();
                    }

                    if ($lead) {
                        $bitrix = new Bitrix();
                        $deal_id = $bitrix->findDeal($lead->lead_id, false);

                        if ($deal_id != 0) {
                            $bitrix->changeDeal($deal_id, [
                                'STAGE_ID' => 'C4:12' // не присутствовал на обучении
                            ]);
                        }

                    }
                }

                $delete_plan = UserDeletePlan::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if ($delete_plan) $delete_plan->delete();

                (new CabinetService)->remove(tenant('id'), $user);
                User::deleteUser($request);
            }
        });

        View::share('title', 'Сотрудник уволен');
        View::share('menu', 'timetrackinguser');

        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    /**
     * Restore user
     */
    public function recoverUser(Request $request)
    {
        if (!Auth::user()) return redirect('/');

        /** @var User $user */
        $user = User::withTrashed()->where('id', $request->id)->first();

        if ($user) {
            $user->deleted_at = null;
            $user->save();
            $user->restore();

//            (new UserService)->restoredUser($user->id);
            $bitrix = new Bitrix();

            $bitrixUser = $bitrix->searchUser($user->email);
            usleep(1000000); // 1 sec
            if ($bitrixUser) $success = $bitrix->recoverUser($bitrixUser['ID']);


            (new CabinetService)->add(tenant('id'), $user, false);

            //add restored_at to users_restored

            $userDesc = UserDescription::query()
                ->where('user_id', $request->id)
                ->whereNotNull('fire_date')
                ->first();
            $userRestor = UserRestored::query()
                ->where('user_id', $request->id)
                ->whereNull('restored_at')
                ->first();
            if ($userRestor) {
                $userRestor->update([
                    "restored_at" => Carbon::now()->format('Y-m-d')
                ]);
            } else {
                UserRestored::query()->create([
                    'user_id' => $request->id,
                    'restored_at' => Carbon::now()->format('Y-m-d'),
                    'destroyed_at' => $userDesc->fire_date,
                    'cause' => $userDesc->fire_cause
                ]);
            }
            if ($userDesc) {
                $userDesc->update([
                    'fire_date' => null,
                    'fire_cause' => null,
                    'fired' => null
                ]);
            }

            Referring::touchReferrerStatus($user);
        }

        View::share('title', 'Сотрудник восстановлен');
        View::share('menu', 'timetrackinguser');


        return view('admin.users.create', $this->preparePersonInputs($request->id));
    }

    /**
     * corp_book_read
     */
    public function corp_book_read(Request $request)
    {
        $user = User::find(auth()->id());
        $user->read_corp_book_at = now();
        $user->notified_at = now();
        $user->save();

        return ['code' => 200];
    }

    /**
     * загрузка аватарки через настроки ( в шаблоне blade ) Kairat
     */
    public function uploadPhoto(Request $request)
    {
        $data = $request["image"];
        $image_array_1 = explode(";", $data);

        $image_array_2 = explode(",", $image_array_1[1]);

        $data = base64_decode($image_array_2[1]);

        $imageName = time() . '.png';

        if (isset($request['user_id']) && $request['user_id'] != 'new_user') {
            $update_user = User::withTrashed()->find($request['user_id']);

            if (!empty($update_user->img_url)) {
                $filename = "users_img/" . $update_user->img_url;

                if (file_exists($filename)) {
                    unlink(public_path('users_img/' . $update_user->img_url));
                }
            }

            $update_user->img_url = $imageName;
            $update_user->save();

            file_put_contents("users_img/$imageName", $data);

            $img = '<img src="' . url('/users_img') . '/' . $imageName . '"  />';

            return response(['src' => $img, 'filename' => $imageName]);


        } elseif ($request['user_id'] == 'new_user') {


            if ($request['file_name'] != 'empty') {
                $filename = "users_img/" . $request['file_name'];

                if (file_exists($filename)) {
                    unlink(public_path('users_img/' . $request['file_name']));
                }
            }


            file_put_contents("users_img/$imageName", $data);

            $img = '<img src="' . asset('users_img/') . '' . $imageName . '"  />';

            return response(['src' => $img, 'filename' => $imageName]);
        }

    }

    /**
     * поиск городов  через профиль Kairat
     */
    public function searchCountry(Request $request)
    {
        $data = DB::table('coordinates')->where('city', 'LIKE', '%' . $request->keyword . '%')->get();
        return response()->json($data);;
    }

    /**
     * загрузка аватарки через профиль в компоненте ( vue.js ) Kairat
     */
    public function uploadImageProfile(Request $request)
    {


        $user = User::withTrashed()->find(auth()->user()->getAuthIdentifier());


        if ($user->img_url) {
            $filename = "users_img/" . $user->img_url;
            if (file_exists($filename)) {
                unlink(public_path('users_img/' . $user->img_url));
            }
        }


        if ($request->file == "null" || $request->file == 'undefined') {
            $user->img_url = null;
            $user->save();

            $img = '<img src="' . url('/users_img') . '/' . 'noavatar.png' . '" alt="avatar" />';

            return response(['img' => $img, 'filename' => 'noavatar.png', 'type' => 0]);

        } else {

            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png'
            ]);


            $upload_path = public_path('users_img/');
            $generated_new_name = time() . '.' . 'png';
            $request->file->move($upload_path, $generated_new_name);
            $user->img_url = $generated_new_name;
            $user->save();

            $img = '<img src="' . url('/users_img') . '/' . $generated_new_name . '" alt="avatar" />';
            return response(['img' => $img, 'filename' => $generated_new_name, 'type' => 1]);
        }


    }

    /**
     * Обновляет посещение пользователя.
     *
     */
    public function online(): void
    {
        $authId = auth()->id();

        if (Auth::check()) {
            $user = User::getUserById($authId);
            $user->last_seen = now();
            $user->save();
        }
    }
}
