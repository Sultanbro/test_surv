<?phpnamespace App\Service\CourseV2;use App\Models\CentralCourse;use App\Models\CentralCourseCat;class LibraryService{    public function categories()    {        return CentralCourseCat::query()            ->whereHas('courses')            ->get();    }    public function latestCourses()    {        return CentralCourse::query()            ->whereNotNull('verified_at')            ->orderByDesc('id')            ->limit(3)            ->get();    }}