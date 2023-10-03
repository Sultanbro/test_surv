<?php

namespace App\Models;

use App\Service\Department\UserService;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;

class CourseResult extends Model
{
    protected $table = 'course_results';

    public $timestamps = true;

    protected $casts = [
        'weekly_progress' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress', // 0 - 100
        'points',  // how many user get
        'started_at',
        'ended_at',
        'weekly_progress' // recent 7 days progress
    ];

    // status
    CONST INITIAL = 0;
    CONST COMPLETED = 1;
    CONST ACTIVE = 2;
    CONST CANCELED = 3;

    CONST STATUSES = [
        0 => 'Запланирован',
        1 => 'Завершил',
        2 => 'Начал',
        3 => 'Отменен',
    ];

    public static $courses;

    /**
     * relation to course_id
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }
    /**
     * relation to user_id
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * get users with course_results in selected group
     */
    public static function getUsers($group_id, $date = null)
    {
        $user_ids = (new UserService)->getEmployeeIds($group_id, $date);

        $users = [];

        self::$courses = Course::get();

        foreach ($user_ids as $key => $user_id) {
            $user = User::withTrashed()
                ->with([
                    'course_results',
                    'course_results.course'
                ])
                ->find($user_id);

            if(!$user) continue;

            $users[] = self::getUserItem($user, $date);
        }

        return [
            'items' => $users,
            'fields' => self::getUserFields()
        ];
    }

    /**
     * user record fields
     */
    private static function getUserFields() {
        $arr = [];
        $arr[] = [
            'key' => 'name',
            'name' => 'Сотрудник',
            'class' => 'text-left'
        ];
        $arr[] = [
            'key' => 'status',
            'name' => 'Статус',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'points',
            'name' => 'Заработано бонусов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress_on_week',
            'name' => 'Прогресс за 7 дней',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'started_at',
            'name' => 'Дата начала',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'ended_at',
            'name' => 'Дата завершения',
            'class' => 'text'
        ];

        return $arr;
    }

    /**
     * Get user record with courseResults
     */
    private static function getUserItem($user, $date) : array
    {
        $arr = [];

        $uc = self::getUserCourses($user);
        $arr['courses'] = $uc['courses'];

        $arr['name'] = $user->last_name . ' ' . $user->name . ' (' . $user->course_results->count() .')';
        $arr['user_id'] = $user->id;



        $arr['status'] = self::STATUSES[$uc['totals']['status']];


        $arr['progress'] = $uc['totals']['progress'] . '%' ;
        $arr['progress_on_week'] = $uc['totals']['progress_on_week'] . '%' ;
        $arr['progress_number'] = $uc['totals']['progress'];
        $arr['points'] = $uc['totals']['points'];
        $arr['max_points'] = $uc['totals']['max_points'];
        $arr['expanded'] = false;
        $arr['started_at'] = $uc['totals']['started_at'];
        $arr['ended_at'] = $uc['totals']['ended_at'];

        return $arr;
    }

    /**
     * get course results of User
     * collapsing list
     */
    private static function getUserCourses($user) : array
    {
        $arrx = [];
        /**
         * total variables
         */
        $points = 0;
        $max_points = 0;
        $status = 2;
        $progress = 0;
        $progress_weekly = 0;
        $progress_count = 0;

        $first_date = $user->course_results->sort(function ($a, $b) {
            return strtotime($a->started_at) < strtotime($b->started_at);
        })->first();

        $last_date = $user->course_results->sort(function ($a, $b) {
            return strtotime($a->ended_at) < strtotime($b->ended_at);
        })->first();

        // get user courses
        $course_ids = self::getCourseIds($user->id);
        /**
         *  order
         */
        foreach($user->course_results as $result) {
            $result->order = $result->course != null ? $result->course->order : 999;
        }

        foreach ($course_ids as $key => $course_id) {
            if(!$user->course_results->where('course_id', $course_id)->first()) {
                $cr = self::create([
                    'user_id' => $user->id,
                    'course_id' => $course_id,
                    'status' => self::INITIAL,
                    'progress' => 0, // 0 - 100
                    'points'=> 0,
                    'started_at' => null,
                    'ended_at' => null,
                ]);
                $cr->order = $key;
                $user->course_results->push($cr);
            }
        }

        $user->course_results = $user->course_results->sortBy('order');

        /**
         * total status
         */
        if($user->course_results->whereIn('status', [1,2])->first()) {
            if($user->course_results->where('status', 2)->first()) {
                $status = self::ACTIVE;
            } else {
                $status = self::COMPLETED;
            }
        } else {
            $status = self::INITIAL;
        }

        /**
         * user courses
         */
        foreach($user->course_results as $result) {

            $course = self::$courses->where('id', $result->course_id)->first();
            if($course) {
                $arr = [];
                $arr['course_id'] = $result->course_id;
                $arr['name'] = $course->name;

                $arr['status'] = self::STATUSES[$result->status];
                $arr['user_id'] = $user->id;
                $arr['is_regressed'] = $result->is_regressed;

                /**
                 * total progress
                 */
                $progress += $result->progress;
                $progress_count++;
                $arr['progress'] = $result->progress > 100 ? '100%' : $result->progress . '%';

                /**
                 * weekly progress
                 */

                $stages_completed = $result->countWeeklyProgress();
                $weekly_progress = $stages_completed > 0 && $result->course != null && $result->course->stages > 0
                    ? round($stages_completed / $result->course->stages * 100, 1)
                    : 0;
                if($weekly_progress > 100) $weekly_progress = 100;
                $arr['progress_on_week'] = $weekly_progress . '%';

                $progress_weekly += $weekly_progress;

                /**
                 * count points and max_points
                 * prepare points string
                 */

                $local_max_points = $course->points != 0 ? $course->points : $result->points;

                $points += $result->points;
                $max_points += $local_max_points;

                $share_of_scored_points = $local_max_points > 0 ? round($result->points / $local_max_points * 100, 1) . '%' : '0%';
                $arr['points'] = $result->points . ' / ' . $local_max_points . ' / ' . $share_of_scored_points;

                /**
                 * dates
                 */
                $arr['started_at'] = $result->started_at ? Carbon::parse($result->started_at)->format('d.m.Y') : '';
                $arr['ended_at'] =  $result->ended_at ? Carbon::parse($result->ended_at)->format('d.m.Y') : '';

                if (isset($course->items) && $course->items->count() > 1) {
                    $arr['items'] = $course->items;
                }

                $arrx[] = $arr;
            }
        }

        $total_progress = $progress_count > 0 ? round($progress / $progress_count) : 0;
        if($total_progress > 100) $total_progress = 100;

        $total_progress_weekly = $progress_count > 0 ? round($progress_weekly / $progress_count) : 0;
        if($total_progress_weekly > 100) $total_progress_weekly = 100;

        return [
            'courses' => $arrx,
            'totals' => [
                'points' => $points,
                'max_points' => $max_points,
                'progress' => $total_progress,
                'progress_on_week' => $total_progress_weekly,
                'status' => $status,
                'started_at' => $first_date && $first_date->started_at ? Carbon::parse($first_date->started_at)->format('d.m.Y') : '',
                'ended_at' => $last_date && $last_date->ended_at ? Carbon::parse($last_date->ended_at)->format('d.m.Y') : '',
            ]
        ];
    }

    /**
     * Count amount of stages user completed recent 7 days
     */
    private function countWeeklyProgress() : int
    {
        $stages = 0;

        if($this->weekly_progress == null) return 0;

        $date = Carbon::now()->addDay();
        for($i = 1; $i <= 7; $i++) {
            $day = $date->subDays(1)->format('Y-m-d');
            if(array_key_exists($day, $this->weekly_progress)) {
                $stages += (int) $this->weekly_progress[$day];
            }
        }

        return $stages;
    }

    /**
     * get groups with progresses in courses
     */
    public static function getGroups($date = null) : array
    {
        $_groups = ProfileGroup::where('active', 1)->get();

        self::$courses = Course::get();

        $groups = [];

        foreach ($_groups as $key => $group) {
            $users = self::getUsers($group->id, $date);
            $groups[] = self::getGroupItem($users, $group);
        }

        return [
            'items' => $groups,
            'fields' => self::getGroupFields()
        ];
    }

    /**
     * get group progress record
     */
    private static function getGroupItem($users, $group) : array
    {
        $points = 0;
        $progress = 0;
        foreach ($users['items'] as $key => $user) {
            $points += $user['points'];
            $progress += $user['progress_number'];
        }

        $arr = [];
        $arr['name'] = $group->name;
        $arr['group_id'] = $group->id;
        $arr['progress'] = $progress . '%';
        $arr['points'] = $points;
        return $arr;
    }

    /**
     * group record fields
     */
    private static function getGroupFields() : array
    {
        $arr = [];
        $arr[] = [
            'key' => 'name',
            'name' => 'Отдел',
            'class' => 'text-left'
        ];
        $arr[] = [
            'key' => 'points',
            'name' => 'Заработано бонусов',
            'class' => 'text'
        ];
        $arr[] = [
            'key' => 'progress',
            'name' => 'Прогресс',
            'class' => 'text'
        ];

        return $arr;
    }


    /**
     * Get active course of user
     *
     * $id for compare is the active course
     *
     * @return Course
     */
    public static function activeCourse($id = 0)
    {
        $user_id = auth()->id();

        /**
         * find course
         */
        $active_course_id = 0;
        $active_course = null;

        $courseIds = self::notFinishedCourses($user_id);

        /**
         * exists not finished course
         */
        if(count($courseIds) > 0) {

            $course = Course::whereIn('id', $courseIds)
                ->orderBy('order')
                ->first();

            $course_id = $course ? $course->id : 0;
            $active_course_id = $course_id;

            if($id != 0) $course_id = $id;

            /**
             * active course
             */
            $active_course = self::where('user_id', $user_id)
                //->whereIn('status', [0,2])
                ->where('course_id', $course_id)
                ->orderBy('status', 'desc')
                ->first();

            if($active_course) {
                if($active_course->status == self::COMPLETED) {
                    $active_course = null;
                }
            } else {

                $active_course = self::create([
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'status' => self::ACTIVE,
                    'progress' => 0, // 0 - 100
                    'points'=> 0,
                    'started_at' => now(),
                    'ended_at' => null,
                ]);
            }
        }

        $course = null;

        /**
         * get poster from S3 Cloud
         */
        if($active_course) {
            $course = Course::with('items')->find($active_course->course_id);

            if($course && $course->img != '' && $course->img != null) {
                $disk = \Storage::disk('s3');

                try {
                    if($course->img != null && $disk->exists($course->img)) {
                        $course->img = $disk->temporaryUrl(
                            $course->img, now()->addMinutes(360)
                        );
                    }
                } catch (\Throwable $e) {
                    // League \ Flysystem \ UnableToCheckDirectoryExistence
                }
            }
        }

        /**
         * active or not
         */
        if($course) {
            $course->is_active = $course->id == $active_course_id || $id == 0;
        }

        return $course;
    }

    /**
     * Get courses user was assigned
     *
     *  order => course_id
     *  [
     *     1 => 2,
     *     2 => 3
     *  ]
     */
    private static function getCourseIds($user_id) : array
    {
        // prepare
        $user = User::withTrashed()->find($user_id);
        $position_id = $user->position_id;

        $groups = $user->inGroups()->pluck('id')->toArray();

        // find course
        $courses = CourseModel::where(function($query) use ($user_id) {
            $query->where('item_model', 'App\\User')
                ->where('item_id', $user_id);
        })
            ->orWhere(function($query) use ($groups) {
                $query->where('item_model', 'App\\ProfileGroup')
                    ->whereIn('item_id', $groups);
            })
            ->orWhere(function($query) use ($position_id) {
                $query->where('item_model', 'App\\Position')
                    ->where('item_id', $position_id);
            })
            ->orWhere(function($query) {
                $query->where('item_model', 0)
                    ->where('item_id', 0);
            })
            ->get()
            ->pluck('course_id')
            ->toArray();

        return Course::whereIn('id', array_unique($courses))
            ->orderBy('order')
            ->get()
            ->pluck('id', 'order')
            ->toArray();
    }

    /**
     * active courses of User
     */
    public static function activeCourses($user_id = null) : array
    {
        $user_id = $user_id ?? auth()->id();

        $courseIds = self::notFinishedCourses($user_id);

        /**
         * Has active courses
         */
        $active_courses = [];
        $disk = \Storage::disk('s3');

        if(count($courseIds) > 0) {
            $active_courses = Course::whereIn('id', $courseIds)
                ->orderBy('order', 'asc')
                ->get()->each(function ($course) use ($disk){
                    $course->text = $course->text != '' || $course->text != null ? trim($course->text) : 'Нет описания';

                    if($course->img != null && $disk->exists($course->img)) {
                        $course->img = $disk->temporaryUrl(
                            $course->img, now()->addMinutes(360)
                        );
                    }
                });
        }

        return $active_courses->toArray();
    }

    /**
     * not finished courses' IDs of user
     * @param int $user_id
     * @return array
     */
    private static function notFinishedCourses($user_id) : array
    {
        /**
         * get all user courses
         */
        $courses = self::getCourseIds($user_id);

        /**
         * user completed courses
         */
        $results = self::where('user_id', $user_id)
            ->whereIn('status', [self::COMPLETED])
            ->get()
            ->pluck('course_id')
            ->toArray();

        $results = array_unique($results);

        /**
         * remove completed courses from all courses
         */
        return array_values(array_diff($courses, $results));
    }
}
