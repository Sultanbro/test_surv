<?phpnamespace App\Service\CourseV2;use App\DTO\CoursesV2\CourseGradePropsDto;use App\KnowBase;use App\Models\Books\Book;use App\Models\UserCourseItemProgress;use App\Models\Videos\VideoPlaylist;use App\Repositories\CoursesV2\MyCourseV2Repository;class MyCourseV2Service{    public function __construct(        public MyCourseV2Repository $repository    )    {    }    public function getCourses()    {        return $this->repository->getCourses();    }    public function storeAssessment(CourseGradePropsDto $dto)    {        return $this->repository->createCourseGrade($dto, auth()->id());    }    public function passV2($request)    {        $user_id = auth()->id();        $courseItemId = $request->input('course_item_id');        /**         * save Course item model         */        $model = UserCourseItemProgress::query()            ->where('user_id', $user_id)            ->where('item_type', $request->type)            ->where('item_id', $request->id)            ->where('course_item_id', $request->course_item_id)            ->first();        if($model) {            $model->status = 1;            $model->save();        } else {            $model = UserCourseItemProgress::query()->create([                'item_type' =>  $request->type,                'item_id' =>  $request->id,                'course_item_id' =>  $request->course_item_id,                'user_id' => $user_id,                'status' =>  1,            ]);        }        /**         * save questions answers         */        $sum_bonus = 0;        foreach ($request->questions as $key => $q) {            if(isset($q['result'])) {                $tr = TestResult::where('test_question_id', $q['id'])                    ->where('user_id', $user_id)                    ->where('course_item_model_id',  $q['result']['course_item_model_id'])                    ->first();                if($tr) {                    $tr->answer = $q['result']['answer'];                    $tr->save();                } else {                    // $tq = TestQuestion::find($q['id']);                    if($q['success']) $sum_bonus += $q['points'];                    TestResult::create([                        'test_question_id' => $q['result']['test_question_id'],                        'answer' => $q['result']['answer'],                        'status' => $q['result']['status'],                        'user_id' => $user_id,                        'course_item_model_id' => $q['result']['course_item_model_id'],                    ]);                }            }        }        /**         * save bonuses         */        if($sum_bonus > 0) {            // get item for what user has got bonus            $item = null;            $type = '';            if($request->type == 1) {                $bs = BookSegment::find($request->id);                if($bs) {                    $item = Book::find($bs->book_id);                    $type = 'За чтение: ';                }            }            if($request->type == 2) {                $video = Video::find($request->id);                if($video) {                    $item = VideoPlaylist::find($video->playlist_id);                    $type = 'За обучение по видео: ';                }            }            if($request->type == 3) {                $item = KnowBase::getTopParent($request->id);                if($item) {                    $type = 'За ответы в Базе знаний: ';                }            }            //save            TestBonus::create([                'date' => date('Y-m-d'),                'user_id' => $user_id,                'amount' => $sum_bonus,                'comment' => $item ? $type . $item->title : 'За обучение',                'course_item_id' => $courseItemId            ]);        }        /**         * count progress and weekly_progress         * save in CourseResult         */        if($request->course_item_id != 0) {            // count progress            $completed_stages = $request->completed_stages;            $count_progress  = $request->all_stages > 0 ? round($completed_stages / $request->all_stages * 100) : 0;            $course_finished  = false;            if($completed_stages >= $request->all_stages) $course_finished = true;            if($count_progress > 100) $count_progress = 100;            // save course result for report            $model = 0;            if($request->type == 1) $model = 'App\Models\Books\BookSegment';            if($request->type == 2) $model = 'App\Models\Videos\Video';            //if($request->type == 3) $model = 'App\KnowBase';            $course_item = CourseItem::where('id', $request->course_item_id)->first();            $cr = $course_item ? CourseResult::where('course_id', $course_item->course_id)                ->where('user_id', $user_id)                ->first() : null;            if($cr) {                if($cr->status == CourseResult::INITIAL) $cr->status = CourseResult::ACTIVE;                if($cr->started_at == null) $cr->started_at = now();                $cr->points += $sum_bonus;                $cr->progress = $count_progress;                // week progress                $wp = $cr->weekly_progress;                if($wp == null) $wp = [];                $sum = 1;                if(array_key_exists(date('Y-m-d'), $wp)) {                    $sum += (int)$wp[date('Y-m-d')];                }                $wp[date('Y-m-d')] = $sum;                $cr->weekly_progress = $wp;                //                if($course_finished) {                    $cr->status = CourseResult::COMPLETED;                    $cr->ended_at = now();//                    event(new TrackCourseItemFinishedEvent($course_item->course, $user_id));                }                $cr->save();            }        }        return [            'item_model' => $model,        ];    }}