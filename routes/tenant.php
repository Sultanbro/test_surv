<?php

declare(strict_types=1);


use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BpartnersController;
use App\Http\Controllers\Admin\CheckListController;
use App\Http\Controllers\Admin\DecompositionController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\GroupAnalyticsController;
use App\Http\Controllers\Admin\KpiController as OldKpiController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\NpsController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QualityController;
use App\Http\Controllers\Admin\QuartalBonusController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\TimetrackingController;
use App\Http\Controllers\Admin\TopController;
use App\Http\Controllers\Admin\TraineeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserFineController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Article\ArticleActionController;
use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Article\Comments\ArticleCommentActionController;
use App\Http\Controllers\Article\Comments\ArticleCommentController;
use App\Http\Controllers\Article\NewsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\AwardTypeController;
use App\Http\Controllers\Birthday\BirthdayController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\CallibroController;
use App\Http\Controllers\Course\RegressCourseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseResultController;
use App\Http\Controllers\Department\UserController as DepartmentUserController;
use App\Http\Controllers\Dictionary\DictionaryController;
use App\Http\Controllers\Files\FileController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntellectController;
use App\Http\Controllers\KnowBaseController;
use App\Http\Controllers\Kpi\BonusController;
use App\Http\Controllers\Kpi\IndicatorController;
use App\Http\Controllers\Kpi\KpiController as KpisController;
use App\Http\Controllers\Kpi\KpiStatController;
use App\Http\Controllers\Kpi\QuartalPremiumController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\NotificationControlller;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileSalaryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UpbookController;
use App\Http\Controllers\Uploads\UploadController;
use App\Http\Controllers\Video\VideoCategoryController;
use App\Http\Controllers\Video\VideoController;
use App\Http\Controllers\Video\VideoGroupController;
use App\Http\Controllers\Video\VideoPlaylistController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Eddir\Messenger\Handlers\MessengerWebSocketHandler;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

/**
 * Custom websocket handler
 */
WebSocketsRouter::webSocket('/messenger/app/{appKey}', MessengerWebSocketHandler::class);

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    \Auth::routes(); 
    

    WebSocketsRouter::webSocket('/messenger/app/{appKey}', MessengerWebSocketHandler::class);

    Route::get('/impersonate/{token}', function ($token) {
        return \Stancl\Tenancy\Features\UserImpersonation::makeResponse($token);
    });
    
  
 

    // Authentication Routes...
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Password reset Routes...
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    // admin routes 
    Route::get('/admino', [\App\Http\Controllers\Admin\AdminController::class, 'index']);    


    Route::get('/newprofile', [ProfileController::class, 'newprofile']);


    Route::get('/send-mail', function() {
        $mailData = [
            'name' => "sad sadsdads",
            'dob' => '123123131231' 
        ];
        
        \Mail::to("abik50000@gmail.com")->send(new \App\Mail\SendInvitation($mailData));
    });
    

    
    Route::any('/bless', function() {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::get('/test-for-check', [TestController::class, 'testMethodForCheck'])->name('testMethodForCheck');
    // Profile
    // Route::any('/', [UserProfileController::class, 'getProfile']); // old
    Route::any('/', [ProfileController::class, 'newprofile']);
    Route::view('/doc', 'docs.index');
    Route::view('/html', 'design');


    Route::group([
        'prefix' => 'profile',
        'as' => 'profile.'
    ], function () {
        // Route::post('/', [UserProfileController::class, 'profile']);
        // Route::any('/', [UserProfileController::class, 'getProfile']);

        Route::get('/', [ProfileController::class, 'newprofile']);
        Route::any('/personal-info', [UserProfileController::class, 'personalInfo']);
        Route::any('/recruter-stats', [UserProfileController::class, 'recruterStatsRates']);
        Route::any('/activities', [UserProfileController::class, 'activities']);
        Route::any('/courses', [UserProfileController::class, 'courses']);
        Route::any('/courses/{id}', [UserProfileController::class, 'course']);
        Route::any('/trainee-report', [UserProfileController::class, 'traineeReport']);
        Route::any('/payment-terms', [UserProfileController::class, 'paymentTerms']);
    });

    Route::post('course/regress', [RegressCourseController::class, 'regress']);

    Route::group([
        'prefix' => 'notifications',
        'as' => 'notifications.'
    ], function () {
        Route::any('/', [NotificationControlller::class, 'get']);
        Route::any('/set-read/', [NotificationControlller::class, 'setRead']);
        Route::any('/set-read-all/', [NotificationControlller::class, 'setAllRead']);
    });


    Route::any('/bonuses', [UserProfileController::class, 'getBonuses']);

    Route::post('logout', [LoginController::class, 'logout']);//->name('logout');


   
    Route::post('/corp_book/set-read/', [UserController::class, 'corp_book_read']); // Прочитать страницу из корп книги @TODO при назначении книги
    Route::any('/timetracking/user/{id}', [UserController::class, 'profile']);
    Route::post('/timetracking/change-password', [UserController::class, 'changePassword']);
    Route::any('/timetracking/get-persons', [UserController::class, 'getpersons']);
    Route::get('/timetracking/create-person', [UserController::class, 'createPerson'])->name('users.create');
    Route::post('/timetracking/person/store', [UserController::class, 'storePerson'])->name('users.store');
    Route::get('/timetracking/edit-person', [UserController::class, 'editperson'])->name('users.edit');
    Route::post('/timetracking/person/update', [UserController::class, 'updatePerson'])->name('users.update');
    Route::post('/timetracking/edit-person/group', [UserController::class, 'editPersonGroup']); // Удалять добавлять пользователя в группы
    Route::post('/timetracking/edit-person/head_in_groups', [UserController::class, 'setUserHeadInGroups']); // Удалять добавлять пользователя руководителем групп
    Route::post('/timetracking/edit-person/book', [UserController::class, 'editPersonBook']); // Удалять добавлять корп книги пользователю
    Route::any('/timetracking/delete-person', [UserController::class, 'deleteUser'])->name('removeUser');
    Route::any('/timetracking/recover-person', [UserController::class, 'recoverUser'])->name('recoverUser');

    ///

    /* Самостоятельная отметка стажеров */
    Route::get('/autocheck/{id}', [TraineeController::class, 'autocheck']); // cтраница со ссылками для отметки стажерами
    Route::post('/autocheck/{id}', [TraineeController::class, 'save']); //
    Route::get('/autochecker', [TraineeController::class, 'autochecker']);  // включить возможность отметки
    Route::post('/autochecker/{id}', [TraineeController::class, 'open']);  // включить возможность отметки


    Route::get('/login-as-employee/{id}', [HomeController::class, 'loginAs']); // войти через test  на страницу профиля
    Route::get('/quiz_after_fire', [HomeController::class, 'quiz_after_fire']); // анкета
    Route::get('/estimate_trainer', [HomeController::class, 'estimate_trainer']);  // анкета
    Route::any('/efd', [HomeController::class, 'estimate_first_day']);  // анкета


    Route::post('/timetracking/quarter/store', [QuartalBonusController::class, 'storePersonQuartal']); /// добавление квартала
    Route::post('/timetracking/quarter/delete', [QuartalBonusController::class, 'deleteQuartal']); /// удаление квартала
    Route::post('/timetracking/quarter/get/quarter/', [QuartalBonusController::class, 'getQuartalBonuses']);

    Route::get('/learning/books', [LearningController::class, 'books']);
    Route::get('/learning/videos', [LearningController::class, 'videos']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses/save-order', [CourseController::class, 'saveOrder']);
    Route::get('/admin/courses/get', [CourseController::class, 'get']);
    Route::post('/admin/courses/delete', [CourseController::class, 'delete']);
    Route::post('/admin/courses/save', [CourseController::class, 'save']);
    Route::post('/admin/courses/create', [CourseController::class, 'create']);
    Route::post('/admin/courses/get-item', [CourseController::class, 'getItem']);
    Route::post('/admin/courses/upload-image', [CourseController::class, 'uploadImage']);

    Route::get('/my-courses', [MyCourseController::class, 'index']);
    Route::get('/my-courses/get', [MyCourseController::class, 'getCourses']);
    Route::get('/my-courses/get/{id}', [MyCourseController::class, 'getMyCourse']);
    Route::post('/my-courses/pass', [MyCourseController::class, 'pass']);

    Route::post('/course-results/get', [CourseResultController::class, 'get']);
    Route::post('/course-results/nullify', [CourseResultController::class, 'nullify']);
    
    Route::get('/glossary/get', [GlossaryController::class, 'get']);
    Route::post('/glossary/save', [GlossaryController::class, 'save']);
    Route::post('/glossary/delete', [GlossaryController::class, 'delete']);

    Route::post('/setting/reset', [SettingController::class, 'reset']);

    Route::get('/playlists/get', [VideoPlaylistController::class, 'get']);
    Route::post('/playlists/video', [VideoPlaylistController::class, 'getVideo']);
    Route::post('/playlists/get', [VideoPlaylistController::class, 'getPlaylist']);
    Route::post('/playlists/add-video', [VideoPlaylistController::class, 'add_video']);
    Route::post('/playlists/save-video', [VideoPlaylistController::class, 'save_video']);
    Route::post('/playlists/save-video-fast', [VideoPlaylistController::class, 'save_video_fast']);

    Route::post('/playlists/delete-video', [VideoPlaylistController::class, 'delete_video']);
    Route::post('/playlists/remove-video', [VideoPlaylistController::class, 'remove_video']);
    Route::post('/playlists/save', [VideoPlaylistController::class, 'save']);
    Route::post('/playlists/save-fast', [VideoPlaylistController::class, 'saveFast']);
    Route::post('/playlists/delete', [VideoPlaylistController::class, 'delete']);
    Route::post('/playlists/save-test', [VideoPlaylistController::class, 'saveTest']);
    Route::post('/playlists/add', [VideoPlaylistController::class, 'add']);
    Route::get('/video_playlists', [VideoPlaylistController::class, 'index']);


    Route::get('/video_playlists/{category}/{playlist}', [VideoPlaylistController::class, 'saveIndex']);
    Route::get('/video_playlists/{category}/{playlist}/{video}', [VideoPlaylistController::class, 'saveIndexVideo']);

    Route::post('/playlists/groups/create', [VideoGroupController::class, 'create']);
    Route::post('/playlists/groups/save', [VideoGroupController::class, 'save']);
    Route::post('/playlists/groups/delete', [VideoGroupController::class, 'delete']);

    Route::post('/playlists/delete-cat', [VideoCategoryController::class, 'delete']);
    Route::post('/playlists/add-cat', [VideoCategoryController::class, 'add']);
    Route::post('/playlists/save-cat', [VideoCategoryController::class, 'save']);

    Route::post('/playlists/video/update', [VideoController::class, 'updateVideo']);
    Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
    Route::post('/videos/save-order', [VideoPlaylistController::class, 'saveOrder']);
    Route::post('/videos/get-playlists-to-move', [VideoPlaylistController::class, 'getPlaylistsToMove']);
    Route::post('/videos/move-to-playlist', [VideoPlaylistController::class, 'moveToPlaylist']);
    Route::post('/videos/add_comment', [VideoController::class, 'add_comment'])->name('videos.add_comment');
    Route::post('/videos/get_comment', [VideoController::class, 'get_comment'])->name('videos.get_comment');
    Route::post('/videos/upload_progress', [VideoController::class, 'upload_progress'])->name('videos.upload_progress');

    // Настройка субдомена
    Route::get('/cabinet', [CabinetController::class, 'index'] );
    Route::get('/cabinet/get', [CabinetController::class, 'get']);
    Route::post('/cabinet/save', [CabinetController::class, 'save']);

    ///Настройка профайл
    Route::post('/profile/save-cropped-image', [UserController::class, 'uploadCroppedImageProfile']); /// загрузка аватарки vue внутри profile
    Route::post('/profile/upload/image/profile/', [UserController::class, 'uploadImageProfile']); /// загрузка обрезаной аватарки vue внутри profile
    Route::any('/profile/upload/edit/', [UserController::class, 'uploadPhoto'])->name('uploadPhoto'); /// загрузка аватарки со стороны Blade javascript
    Route::any('/profile/edit/user/cart/', [UserController::class, 'editUserProfile']); ///profile save name,last_name,date ///profile save name,last_name,date
    Route::post('/profile/remove/card/', [UserController::class, 'removeCardProfile']); ///удаление карты индивидуально
    Route::post('/profile/country/city/', [UserController::class, 'searchCountry']); /// поиск городов через Профиль

    // Книги
    Route::get('/admin/upbooks', [UpbookController::class, 'index']);
    Route::post('/upbooks/save-cat', [UpbookController::class, 'saveCat']);
    Route::get('/admin/upbooks/get', [UpbookController::class, 'admin_get']);
    Route::post('/admin/upbooks/category/create', [UpbookController::class, 'createCategory']);
    Route::post('/admin/upbooks/category/delete', [UpbookController::class, 'deleteCategory']);
    Route::post('/admin/upbooks/segments/get', [UpbookController::class, 'getSegments']);
    Route::post('/admin/upbooks/save', [UpbookController::class, 'save']);
    Route::post('/admin/upbooks/update', [UpbookController::class, 'update']);
    Route::post('/admin/upbooks/delete', [UpbookController::class, 'delete']);
    Route::post('/admin/upbooks/segments/save', [UpbookController::class, 'saveSegment']);
    Route::post('/admin/upbooks/segments/delete', [UpbookController::class, 'deleteSegment']);

    Route::post('/playlists/delete-question',[VideoPlaylistController::class, 'deleteQuestion']);


    // @TODO CHECK AND DELETE THIS ROUTES
    Route::get('/bp_books', [BookController::class, 'index']);
    Route::get('/bp_books/groups', [BookController::class, 'groups']);
    Route::post('/bp_books/groups', [BookController::class, 'group']);
    Route::post('/bp_books/groups/add', [BookController::class, 'createBookGroup']);
    Route::post('/bp_books/groups/delete', [BookController::class, 'deleteGroup']);
    Route::post('/bp_books/groups/add_books_to_group', [BookController::class, 'addBooksToGroup']);
    Route::post('/bp_books/books', [BookController::class, 'books']);
    Route::post('/bp_books/book/add', [BookController::class, 'createBook']);
    Route::post('/bp_books/book/edit', [BookController::class, 'editBook']);
    Route::post('/bp_books/book/delete', [BookController::class, 'deleteBook']);
    Route::post('/bp_books/position_groups', [BookController::class, 'positionGroups']);
    Route::post('/bp_books/position_groups/save', [BookController::class, 'savePositionGroups']);

    // База знаний
    Route::get('/kb', [KnowBaseController::class, 'index']);
    Route::get('/kb/get', [KnowBaseController::class, 'get']);
    
    Route::post('/settings/get/', [SettingController::class, 'getSettings']);
    Route::post('/settings/save', [SettingController::class, 'saveSettings']);

    Route::post('/kb/get', [KnowBaseController::class, 'getPage']);
    Route::post('/kb/search', [KnowBaseController::class, 'search']);
    Route::get('/kb/get-archived', [KnowBaseController::class, 'getArchived']);
    Route::post('/kb/tree', [KnowBaseController::class, 'getTree']);
    Route::post('/kb/page/update', [KnowBaseController::class, 'updatePage']);
    Route::post('/kb/page/delete-section', [KnowBaseController::class, 'deleteSection']);
    Route::post('/kb/page/restore-section', [KnowBaseController::class, 'restoreSection']);
    Route::post('/kb/page/add-section', [KnowBaseController::class, 'addSection']);
    Route::post('/kb/page/save-order', [KnowBaseController::class, 'saveOrder']);
    Route::post('/kb/page/save-test', [KnowBaseController::class, 'saveTest']);
    Route::post('/kb/page/create', [KnowBaseController::class, 'createPage']);
    Route::post('/kb/page/delete', [KnowBaseController::class, 'deletePage']);
    Route::post('/kb/page/update-section', [KnowBaseController::class, 'updateSection']);
    Route::post('/kb/page/get-access', [KnowBaseController::class, 'getAccess']);

    Route::get('/kb/get-settings', [KnowBaseController::class, 'getSettings']);
    Route::post('/kb/save-settings', [KnowBaseController::class, 'saveSettings']);


    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/get', [PermissionController::class, 'get']); 

    Route::post('/permissions/create-role', [PermissionController::class, 'createRole']);
    Route::post('/permissions/update-role', [PermissionController::class, 'updateRole']);
    Route::post('/permissions/update-target', [PermissionController::class, 'updateTarget']);
    Route::post('/permissions/delete-target', [PermissionController::class, 'deleteTarget']);
    Route::post('/permissions/delete-role', [PermissionController::class, 'deleteRole']);



    Route::get('/test', [TestController::class, 'test'])->name('test');
    Route::get('/wami', [TestController::class, 'send_whatsapp']);

    Route::post('/timetracking/settings/groups/importexcel', [GroupsController::class, 'import']);
    Route::post('/timetracking/settings/groups/importexcel/save', [GroupsController::class, 'saveTimes']);
    Route::any('/timetracking/users/bonus/save', [GroupsController::class, 'saveBonuses']);

    Route::post('/timetracking/analytics/activity/importexcel', [ActivityController::class, 'import']);
    Route::post('/timetracking/analytics/activity/importexcel/save', [ActivityController::class, 'saveTimes']);

    Route::post('/timetracking/analytics/decomposition/save', [DecompositionController::class, 'save']);
    Route::delete('/timetracking/analytics/decomposition/delete', [DecompositionController::class, 'delete']);

    Route::get('/timetracking/fine', [FineController::class, 'index']);
    Route::put('/timetracking/fine', [FineController::class, 'update']);

    Route::get('/timetracking/exam', [ExamController::class, 'index']);
    Route::post('/timetracking/exam', [ExamController::class, 'getexams']);
    Route::post('/timetracking/exam/update', [ExamController::class, 'update']);

    Route::post('/timetracking/kpi_save', [OldKpiController::class, 'saveKPI']);
    Route::post('/timetracking/kpi_get', [OldKpiController::class, 'getKPI']);
    Route::post('/timetracking/kpi_save_individual', [OldKpiController::class, 'saveKpiIndividual']);
    Route::post('/timetracking/kpi_get_individual', [OldKpiController::class, 'getKpiIndividual']);

    Route::any('/estimate_your_trainer', [NpsController::class, 'estimate_your_trainer']); // анкета
    Route::get('/timetracking/nps', [NpsController::class, 'index']);
    Route::post('/timetracking/nps', [NpsController::class, 'fetch']);


    // Учет времени @TODO DIVIDE to controllers by context
    Route::any('/timetracking', [TimetrackingController::class, 'index']);
    Route::any('/timetracking/fines', [TimetrackingController::class, 'fines']);
    Route::any('/timetracking/info', [TimetrackingController::class, 'info']);
    Route::any('/timetracking/set-day', [TimetrackingController::class, 'setDay']);
    Route::any('/timetracking/history', [TimetrackingController::class, 'getHistory']);
    Route::any('/timetracking/settings', [TimetrackingController::class, 'settings']);
    Route::any('/timetracking/settings/positions', [TimetrackingController::class, 'positions']);
    Route::any('/timetracking/settings/positions/get', [TimetrackingController::class, 'getPosition']);
    Route::any('/timetracking/settings/positions/save', [TimetrackingController::class, 'savePositions']);
    Route::post('/timetracking/settings/positions/add', [TimetrackingController::class, 'addPosition']);
    Route::post('/timetracking/settings/positions/delete', [TimetrackingController::class, 'deletePosition']);
    Route::post('timetracking/settings/get_time_addresses', [TimetrackingController::class, 'getTimeAddresses']);
    Route::post('timetracking/settings/save_time_addresses', [TimetrackingController::class, 'saveTimeAddresses']);
    Route::any('/timetracking/settings/add', [TimetrackingController::class, 'addsettings']);
    Route::any('/timetracking/settings/delete', [TimetrackingController::class, 'deletesettings']);
    Route::any('/timetracking/user/save', [TimetrackingController::class, 'saveprofile']);
    Route::post('/timetracking/settings/notifications/update', [TimetrackingController::class, 'updateNotificationTemplate']);
    Route::get('/timetracking/settings/notifications/get', [TimetrackingController::class, 'getNotificationTemplates']);
    Route::post('/timetracking/settings/notifications/user', [TimetrackingController::class, 'getUserNotifications']);
    Route::post('/timetracking/settings/notifications/user/save', [TimetrackingController::class, 'saveUserNotifications']);
    Route::get('/timetracking/reports', [TimetrackingController::class, 'reports']);
    Route::post('/timetracking/reports', [TimetrackingController::class, 'getReports']);
    Route::post('/timetracking/reports/update/day', [TimetrackingController::class, 'updateTimetrackingDay']);
    Route::any('/timetracking/starttracking', [TimetrackingController::class, 'timetracking']);
    Route::any('/timetracking/status', [TimetrackingController::class, 'trackerstatus']);
    Route::any('/timetracking/group/save', [TimetrackingController::class, 'savegroup']);
    Route::any('/timetracking/group/delete', [TimetrackingController::class, 'deletegroup']);
    Route::any('/timetracking/users', [TimetrackingController::class, 'getusersgroup']);
    Route::any('/timetracking/users/group/save', [TimetrackingController::class, 'saveusersgroup']);
    Route::any('/timetracking/groups', [TimetrackingController::class, 'getgroups']);
    Route::post('/timetracking/groups/restore', [TimetrackingController::class, 'restoreGroup']);
    Route::any('/timetracking/reports/add-editors', [TimetrackingController::class, 'usereditreports']);
    Route::any('/timetracking/reports/get-editors', [TimetrackingController::class, 'modalcheckuserrole']);
    Route::any('/timetracking/reports/check-user', [TimetrackingController::class, 'checkuserrole']);
    Route::any('/timetracking/reports/enter-report', [TimetrackingController::class, 'enterreport']);
    Route::post('/timetracking/reports/enter-report/setmanual', [TimetrackingController::class, 'enterreportManually']);
    Route::any('/timetracking/zarplata-table', [TimetrackingController::class, 'zarplatatable']);
    Route::post('/order-persons-to-group', [TimetrackingController::class, 'orderPersonsToGroup']); // Заказ сотрудников в группы для Руководителей
    Route::post('/timetracking/apply-person', [TimetrackingController::class, 'applyPerson']); // Принятие на штат стажера
    Route::post('/timetracking/get-totals-of-reports', [TimetrackingController::class, 'getTotalsOfReports']);

    Route::group([
        'prefix' => 'group-user',
    ], function(){
        Route::post('/save', [TimetrackingController::class, 'addUsers']);
        Route::post('/drop', [TimetrackingController::class, 'dropUsers']);
    });

    Route::group(['prefix' => 'bitrix'], function (){
        Route::get('/tasks/list', [\App\Http\Controllers\IntegrationController::class, 'getAllTasksFromBitrix']);
        Route::get('/leads/list', [\App\Http\Controllers\IntegrationController::class, 'getLeads']);
    });

    Route::group([
        'prefix' => 'department',
        'as'     => 'department.'
    ], function () {
        Route::get('/users/{id?}/{date?}', [DepartmentUserController::class, 'getUsers'])->name('users');
        Route::get('/employees/{id?}/{date?}', [DepartmentUserController::class, 'getEmployees'])->name('employees');
        Route::get('/trainees/{id?}/{date?}', [DepartmentUserController::class, 'getTrainees'])->name('trainees');
        Route::get('/fired-users/{id?}/{date?}', [DepartmentUserController::class, 'getFiredUsers'])->name('fired-users');
        Route::get('/fired-trainees/{id?}/{date?}', [DepartmentUserController::class, 'getFiredTrainees'])->name('fired-trainees');

        Route::get('/check/user/{id}', [DepartmentUserController::class, 'userInGroup']);
    });

    /**
     * Отслеживаем посещение стажеров на стажировке.
     */
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance', [AttendanceController::class, 'getDayAttendance']);


    Route::get('/bitrix/tasks/list', [\App\Http\Controllers\IntegrationController::class, 'getAllTasksFromBitrix']);

    Route::get('/timetracking/top', [TopController::class, 'index']);
    Route::post('/timetracking/top', [TopController::class, 'fetch']);
    Route::post('/timetracking/top/save_top_value', [TopController::class, 'saveTopValue']);
    Route::post('/timetracking/top/get-rentability', [TopController::class, 'getRentability']);
    Route::post('/timetracking/top/get-rentability-on-month', [TopController::class, 'getRentabilityOnMonth']);
    Route::post('/timetracking/top/create_gauge', [TopController::class, 'createGauge']);
    Route::post('/timetracking/top/get_activities', [TopController::class, 'getActivities']);
    Route::post('/timetracking/top/delete_gauge', [TopController::class, 'deleteGauge']);
    Route::post('/timetracking/top/save_rent_max', [TopController::class, 'saveRentMax']);
    Route::post('/timetracking/top/save_group_plan', [TopController::class, 'saveGroupPlan']);
    Route::post('/timetracking/top/top_edited_value/update', [TopController::class, 'updateTopEditedValue']);
    Route::post('/timetracking/top/proceeds/update', [TopController::class, 'updateProceeds']);


    Route::any('/timetracking/quality-control/', [QualityController::class, 'index']);


    Route::any('/timetracking/quality-control/export', [QualityController::class, 'exportExcel']);
    Route::any('/timetracking/quality-control/change-type', [QualityController::class, 'changeType']);
    Route::any('/timetracking/quality-control/exportall', [QualityController::class, 'exportAllExcel']);
    Route::post('/timetracking/quality-control/save', [QualityController::class, 'saveRecord']);
    Route::post('/timetracking/quality-control/saveweekly', [QualityController::class, 'saveWeeklyRecord']);
    Route::post('/timetracking/quality-control/delete', [QualityController::class, 'deleteRecord']);
    Route::post('/timetracking/quality-control/records', [QualityController::class, 'getRecords']);
    Route::post('/timetracking/quality-control/crits/save', [QualityController::class, 'saveCrits']);


    Route::get('/timetracking/salaries', [SalaryController::class, 'index']);
    Route::get('/timetracking/salaries/export', [SalaryController::class, 'exportExcel']);
    Route::post('/timetracking/salaries/get-total', [SalaryController::class, 'getTotal']);
    Route::post('/timetracking/salaries', [SalaryController::class, 'salaries']);
    Route::post('/timetracking/salaries/update', [SalaryController::class, 'update']);
    Route::post('/timetracking/salaries/recalc', [SalaryController::class, 'recalc']);
    Route::post('/timetracking/salaries/edit-premium', [SalaryController::class, 'editPremium']);
    Route::post('/timetracking/salaries/approve-salary', [SalaryController::class, 'approveSalary']);
    Route::post('/timetracking/salaries/bonuses', [SalaryController::class, 'bonuses']);


    Route::any('/timetracking/analytics/save-call-base', [GroupAnalyticsController::class, 'saveCallBase']);
    Route::any('/timetracking/analytics', [GroupAnalyticsController::class, 'index']);
    Route::any('/timetracking/analytics/skypes/{id}', [GroupAnalyticsController::class, 'redirectToBitrixDeal']);
    Route::any('/timetracking/getanalytics', [GroupAnalyticsController::class, 'getanalytics']);
    Route::any('/timetracking/analytics/invite-users', [GroupAnalyticsController::class, 'inviteUsers']); // Приглашение стажеров
    Route::post('/timetracking/analytics/recruting/create-lead', [GroupAnalyticsController::class, 'createRecrutingLead']); // Создание лидов вручную
    Route::post('/timetracking/analytics/recruting/change-profile', [GroupAnalyticsController::class, 'changeRecruiterProfile']); // Сменить профиль рекрутера
    Route::any('/timetracking/get_kpi_totals', [GroupAnalyticsController::class, 'get_kpi_totals']);
    Route::any('/timetracking/update-settings', [GroupAnalyticsController::class, 'update']);
    Route::any('/timetracking/update-settings-extra', [GroupAnalyticsController::class, 'updateExtra']);
    Route::post('/timetracking/update-activity-total', [GroupAnalyticsController::class, 'update_activity_total']);
    Route::any('/timetracking/update-settings-individually', [GroupAnalyticsController::class, 'updateIndividually']);
    Route::get('/timetracking/analytics/activity/export', [GroupAnalyticsController::class, 'exportActivityExcel']);

    Route::get('/hr/ref-links', [GroupAnalyticsController::class, 'getRefLinks']);
    Route::post('/hr/ref-links/save', [GroupAnalyticsController::class, 'saveRefLinks']);

    Route::any('/timetracking/an', [AnalyticsController::class, 'index']);
    Route::any('/timetracking/analytics-page/getanalytics', [AnalyticsController::class, 'get']);
    Route::get('/timetracking/analytics/activity/exportxx', [AnalyticsController::class, 'exportActivityExcel']);
    Route::post('/timetracking/analytics/add-row', [AnalyticsController::class, 'addRow']);
    Route::post('/timetracking/analytics/delete-row', [AnalyticsController::class, 'deleteRow']);
    Route::post('/timetracking/analytics/dependency/remove', [AnalyticsController::class, 'removeDependency']);
    Route::post('/timetracking/analytics/edit-stat', [AnalyticsController::class, 'editStat']);
    Route::post('/timetracking/analytics/set-decimals', [AnalyticsController::class, 'setDecimals']);
    
    Route::post('/timetracking/analytics/new-group', [AnalyticsController::class, 'newGroup']);
    Route::post('/timetracking/analytics/create-activity', [AnalyticsController::class, 'createActivity']);
    Route::post('/timetracking/analytics/edit-activity', [AnalyticsController::class, 'editActivity']);
    Route::post('/timetracking/analytics/update-stat', [AnalyticsController::class, 'updateUserStat']);
    Route::post('/timetracking/analytics/save-cell-activity', [AnalyticsController::class, 'saveCellActivity']);
    Route::post('/timetracking/analytics/save-cell-time', [AnalyticsController::class, 'saveCellTime']);
    Route::post('/timetracking/analytics/save-cell-sum', [AnalyticsController::class, 'saveCellSum']);
    Route::post('/timetracking/analytics/save-cell-avg', [AnalyticsController::class, 'saveCellAvg']);
    Route::post('/timetracking/analytics/change_order', [AnalyticsController::class, 'change_order']);
    Route::post('/timetracking/analytics/delete_activity', [AnalyticsController::class, 'delete_activity']);
    Route::post('/timetracking/analytics/add-depend', [AnalyticsController::class, 'add_depend']);
    Route::post('/timetracking/analytics/archive_analytics', [AnalyticsController::class, 'archive_analytics']);
    Route::post('/timetracking/analytics/restore_analytics', [AnalyticsController::class, 'restore_analytics']);
    Route::post('/timetracking/analytics/add-formula-1-31', [AnalyticsController::class, 'addFormula_1_31']);
    Route::post('/timetracking/analytics/add-remote-inhouse', [AnalyticsController::class, 'addRemoteInhouse']);
    Route::post('/timetracking/analytics/add-salary', [AnalyticsController::class, 'addSalary']);
    Route::post('/timetracking/getactivetrainees',[GroupAnalyticsController::class,'getActiveTrainees']);

    Route::any('/timetracking/user-statistics-by-month', [AnalyticsController::class, 'getUserStatisticsByMonth']);
    /**
     * Редактирование бонусов
     */
    Route::group([
        'prefix'     => 'bonus',
        'as' => 'bonus.'
    ], function(){
        Route::post('get',[BonusController::class,'get'])->name('get');
        Route::post('user',[BonusController::class,'getUserBonuses'])->name('getUserBonuses');
        Route::post('save',[BonusController::class,'save'])->name('save');
        Route::put('update',[BonusController::class,'update'])->name('update');
        Route::delete('delete/{id}',[BonusController::class,'delete'])->name('delete');
    });

    /**
     * Редактирование квартальной премии
     */
    Route::group([
        'prefix'     => 'quartal-premiums',
//        'middleware' => 'auth'
    ], function(){
        Route::post('get',[QuartalPremiumController::class,'get'])->name('quartal-premium.get');
        Route::post('save',[QuartalPremiumController::class,'save'])->name('quartal-premium.save');
        Route::put('update',[QuartalPremiumController::class,'update'])->name('quartal-premium.update');
        Route::delete('delete/{id}',[QuartalPremiumController::class,'destroy']);
    });

    /**
     * Статистика для KPI.
     */
    Route::group([
        'prefix' => 'statistics',
        'as'     => 'kpi-statistic.'
    ], function (){
        Route::get('kpi/user/{id}', [KpiStatController::class, 'show'])->name('index');
        Route::get('kpi/users/', [KpiStatController::class, 'fetchGroups'])->name('fetch');
        Route::any('kpi', [KpiStatController::class, 'fetchKpis'])->name('fetchKpis');
        Route::any('bonuses', [KpiStatController::class, 'fetchBonuses'])->name('fetchBonuses');
        Route::any('quartal-premiums', [KpiStatController::class, 'fetchQuartalPremiums'])->name('fetchQuartalPremiums');
        Route::any('workdays', [KpiStatController::class, 'workdays']);
        Route::post('update-stat', [KpiStatController::class, 'updateStat'])->name('updateStat');
        Route::get('activities',[KpiStatController::class,'getActivities'])->name('getActivites');
    });

    /**
     * Редактирование показателей
     */
    Route::group([
        'prefix'     => 'activities',
        'as'         => 'activities.',
        'middleware' => 'superuser'
    ], function(){
        Route::post('/get', [IndicatorController::class, 'fetch'])->name('all');
        Route::get('/{id}', [IndicatorController::class, 'get'])->name('one');
        Route::post('save',[IndicatorController::class,'save'])->name('save');
        Route::put('update',[IndicatorController::class,'update'])->name('update');
        Route::delete('delete/{id}',[IndicatorController::class,'delete'])->name('delete');
    });

    /**
     * Типы награды для сотрудников.
     */
    Route::group([
        'prefix' => 'award-types',
        'as'     => 'award-types.',
    ], function () {
        Route::get('/get', [AwardTypeController::class, 'index'])->name('get');
        Route::post('/store', [AwardTypeController::class, 'store'])->name('store');
        Route::put('/update/{awardType}', [AwardTypeController::class, 'update'])->name('update');
        Route::delete('/delete/{awardType}', [AwardTypeController::class, 'destroy'])->name('destroy');
    });

    /**
     * Награды для сотрудников.
     */
    Route::group([
        'prefix' => 'awards',
        'as'     => 'awards.',
    ], function () {
        Route::post('/reward', [AwardController::class, 'reward'])->name('reward');
        Route::delete('/reward-delete', [AwardController::class, 'deleteReward'])->name('delete-reward');
        Route::get('/my', [AwardController::class, 'myAwards'])->name('my-awards');
        Route::get('/course', [AwardController::class, 'courseAward'])->name('course-awards');
        Route::get('/type', [AwardController::class, 'awardsByType'])->name('type-awards');
        Route::get('/get', [AwardController::class, 'index'])->name('get');
        Route::get('/get/{award}', [AwardController::class, 'show'])->name('show');
        Route::post('/store', [AwardController::class, 'store'])->name('store');
        Route::put('/update/{award}', [AwardController::class, 'update'])->name('update');
        Route::delete('/delete/{award}', [AwardController::class, 'destroy'])->name('destroy');
    });


    Route::get('/books/{id?}', [BpartnersController::class, 'books']);
    Route::any('/pages/update/', [BpartnersController::class, 'pagesupdate']);
    Route::any('/pages/delete/', [BpartnersController::class, 'pagesdelete']);
    Route::any('/upload/images/', [BpartnersController::class, 'uploadimages']);
    Route::any('/upload/audio/', [BpartnersController::class, 'uploadaudio']);


    /* Intellect Recruiting */
    Route::get('/bpr/{hash}', [IntellectController::class, 'contract']);
    Route::post('/bpr/{hash}', [IntellectController::class, 'contract']);
    Route::get('/bpcontract', [IntellectController::class, 'contract']);
    Route::any('/bp/job/agreement', [IntellectController::class, 'contract']);
    Route::any('/bp/job/skype', [IntellectController::class, 'skype']);
    Route::any('/bp/choose_time', [IntellectController::class, 'choose_time']);

    // Controllers with one method

    Route::post('/file/upload', [FileUploadController::class, 'uploadLargeFiles'])->name('files.upload.large');

    Route::get('/corp_book/{id}', [LinkController::class, 'opened_corp_book']);
    Route::any('/timetracking/analytics/funnels', [LeadController::class, 'funnel_segment']);
    Route::post('/timetracking/user-fine', [UserFineController::class, 'update']);
    Route::post('/user/save/answer', [ProfileController::class, 'saveAnswer']);
    Route::post('/position/save/desc', [PositionController::class, 'savePositionDesc']);

    
    Route::get('/maps', [MapsController::class, 'index'])->name('maps');
    Route::post('/selected-country/search/', [MapsController::class, 'selectedCountryAjaxSearch']);


    Route::post('/checklist/tasks', [ChecklistController::class, 'getTasks']);
    Route::post('/checklist/save', [ChecklistController::class, 'saveTasks']);
    Route::post('/checklist/get-checklist-by-user',[ChecklistController::class,'getChecklistByUser']);
    Route::post('/checklist/save-checklist',[ChecklistController::class, 'saveChecklist']);

    Route::post('/timetracking/settings/add/check', [CheckListController::class, 'store']); /// добавление Чек листа
    Route::get('/timetracking/settings/list/check', [CheckListController::class, 'listViewCheck']); /// список Чек листов
    Route::post('/timetracking/settings/delete/check', [CheckListController::class, 'deleteCheck']); /// удаление Чек листа по ИД
    Route::post('/timetracking/settings/edit/check', [CheckListController::class, 'editCheck']); /// Открыть  Чек лист по ИД

    Route::get('/timetracking/settings/auth/check/user', [CheckListController::class, 'viewAuthCheck']); /// со стораны пользователя если есть будет показывать
    Route::post('/timetracking/settings/auth/check/user/send', [CheckListController::class, 'sendAuthCheck']); /// со стораны пользователя Выполнить сохр в отчет
    Route::post('/timetracking/settings/auth/check/user/responsibility', [CheckListController::class, 'responsibility']); ///   Добавить ответственного лица
    Route::post('/timetracking/settings/get/modal/', [CheckListController::class, 'getModal']); ///   Получить пользователей
    Route::post('/timetracking/settings/auth/check/search/selected', [CheckListController::class, 'searchSelected']); 

    Route::post('/timetracking/settings/edit/check/save/', [CheckListController::class, 'editSaveCheck']); /// Редактировать Сохранить Чек листа по ИД




    Route::get('/superselect/get', [PermissionController::class, 'superselect']);
    Route::get('/superselect/get-alt', [PermissionController::class, 'superselectAlt']);
    Route::get('/callibro/login', [CallibroController::class, 'login']);

    Route::post('/profile/salary/get', [ProfileSalaryController::class, 'get']);

   

    Route::group([
        'prefix'    => 'kpi',
        'as'        => 'kpi.',
        'middleware' => 'auth'
    ], function (){
        Route::get('/', [KpisController::class, 'index'])->name('index');
        Route::post('/get', [KpisController::class, 'getKpis'])->name('get');
        Route::post('/save', [KpisController::class, 'save'])->name('save');
        Route::put('/update', [KpisController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [KpisController::class, 'delete'])->name('delete');
    });


    Route::prefix('news')
        ->name('articles.')
        ->middleware([
            'auth',
        ])
        ->group(function () {
            Route::get('', [NewsController::class, 'index'])->name('index');

            Route::get('/get', [ArticleController::class, 'index'])
                ->name('index');

            Route::get('{article_id}', [ArticleController::class, 'show'])
                ->name('show');

            Route::post('', [ArticleController::class, 'store'])
                ->name('store');

            Route::put('{article_id}', [ArticleController::class, 'update'])
                ->name('update');

            Route::delete('{article_id}', [ArticleController::class, 'delete'])
                ->name('delete');

            Route::prefix('{article_id}')
                ->name('actions.')
                ->group(function () {

                    Route::post('like', [ArticleActionController::class, 'like'])
                        ->name('like');

                    Route::post('favourite', [ArticleActionController::class, 'favourite'])
                        ->name('favourite');

                    Route::post('pin', [ArticleActionController::class, 'pin'])
                        ->name('pin');

                    Route::post('views', [ArticleActionController::class, 'views'])
                        ->name('views');
                });

            Route::prefix('{article_id}/comments')
                ->name('comments.')
                ->group(function () {

                    Route::get('', [ArticleCommentController::class, 'index'])
                        ->name('index');

                    Route::post('', [ArticleCommentController::class, 'store'])
                        ->name('store');

                    Route::delete('{comment_id}', [ArticleCommentController::class, 'delete'])
                        ->name('delete');

                    Route::prefix('{comment_id}')
                        ->name('actions.')
                        ->group(function () {

                            Route::post('like', [ArticleCommentActionController::class, 'like'])
                                ->name('like');

                            Route::post('reaction', [ArticleCommentActionController::class, 'reaction'])
                                ->name('reaction');
                        });
                });
        });

    Route::middleware('auth')
        ->get('/me', [NewsController::class, 'user']);

    Route::prefix('dictionaries')
        ->name('dictionaries.')
        ->middleware([
            'auth',
        ])
        ->group(function () {

            Route::get('', [DictionaryController::class, 'index'])
                ->name('index');
        });

    Route::prefix('birthdays')
        ->name('birthdays.')
        ->middleware([
            'auth',
        ])
        ->group(function () {

            Route::get('', [BirthdayController::class, 'index'])
                ->name('index');

            Route::post('{user}/send-gift', [BirthdayController::class, 'sendGift'])
                ->name('send_gift');
        });

    Route::prefix('files')
        ->name('files.')
        ->middleware([
            'auth',
        ])
        ->group(function () {

            Route::post('', [FileController::class, 'store'])
                ->name('store');

            Route::delete('{file_id}', [FileController::class, 'delete'])
                ->name('delete');
        });

    Route::prefix('uploads')
        ->name('uploads.')
        ->middleware([
            'auth:web',
        ])
        ->group(function () {

            Route::post('', [UploadController::class, 'store'])
                ->name('store');
        });




    Route::any('/getnewimage',[UserController::class,'getProfileImage']);

    Route::group([
        'prefix'   => 'messenger/api',
    ], function() {

        /**
         * Get chats list
         */
        Route::get('/v2/chats', 'ChatsController@fetchChats')->name('api.chats.fetch');

        /**
         * Get users list
         */
        Route::get('/v2/users', 'ChatsController@fetchUsers')->name('api.users.fetch');

        /**
         * Search chat by name
         */
        Route::get('/v2/search/chats', 'ChatsController@search')->name('api.chats.search');

        /**
         * Search messages by text
         */
        Route::get('/v2/search/messages', 'MessagesController@searchMessages')->name('api.messages.search');

        /**
         * Get chat messages
         */
        Route::get('/v2/chat/{chat_id}/messages', 'MessagesController@fetchMessages')->name('api.messages.fetch');

        /**
         * Get private chat info
         */
        Route::get('/v2/private/{user_id}', 'ChatsController@getPrivateChat')->name('api.v2.getPrivateChat');

        /**
         * Get chat info
         */
        Route::get('/v2/chat/{chat_id}', 'ChatsController@getChat')->name('api.v2.getChat');

        /**
         * Send message
         */
        Route::post('/v2/chat/{chat_id}/messages', 'MessagesController@sendMessage')->name('api.v2.sendMessage');

        /**
         * Edit message. Message id should be integer
         */
        Route::post('/v2/message/{message_id}', 'MessagesController@editMessage')->name('api.v2.editMessage')->whereNumber('message_id');

        /**
         * Delete message
         */
        Route::delete('/v2/message/{message_id}', 'MessagesController@deleteMessage')->name('api.v2.deleteMessage');

        /**
         * Pin message
         */
        Route::post('/v2/message/{message_id}/pin', 'MessagesController@pinMessage')->name('api.v2.pinMessage');

        /**
         * Unpin message
         */
        Route::delete('/v2/message/{message_id}/pin', 'MessagesController@unpinMessage')->name('api.v2.unpinMessage');

        /**
         * Pin chat
         */
        Route::post('/v2/chat/{chat_id}/pin', 'ChatsController@pinChat')->name('api.v2.pinChat');

        /**
         * Unpin chat
         */
        Route::delete('/v2/chat/{chat_id}/pin', 'ChatsController@unpinChat')->name('api.v2.unpinChat');

        /**
         * Create chat
         */
        Route::post('/v2/chat', 'ChatsController@createChat')->name('api.v2.createChat');

        /**
         * Remove chat
         */
        Route::delete('/v2/chat/{chat_id}', 'ChatsController@removeChat')->name('api.v2.removeChat');

        /**
         * Leave chat
         */
        Route::post('/v2/chat/{chat_id}/leave', 'ChatsController@leaveChat')->name('api.v2.leaveChat');

        /**
         * Add user to chat
         */
        Route::post('/v2/chat/{chat_id}/addUser', 'ChatsController@addUser')->name('api.v2.addUser');

        /**
         * Remove user from chat
         */
        Route::post('/v2/chat/{chat_id}/removeUser/{user_id}', 'ChatsController@removeUser')->name('api.v2.removeUser');

        /**
         * Edit chat
         */
        Route::post('/v2/chat/{chat_id}/edit', 'ChatsController@editChat')->name('api.v2.editChat');

        /**
         * Set messages as read
         */
        Route::post('/v2/messages/read', 'MessagesController@setMessagesAsRead')->name('api.v2.setMessagesAsRead');

        /**
         * Upload file
         */
        Route::post('/v2/chat/{chat_id}/upload', 'FilesController@upload')->name('api.v2.upload');

                                
    });
});





/**
 * 
 * API routes
 * instead of api.php
 * 
 */
Route::middleware([
    'api',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::group([
        'prefix'   => 'api',
    ], function() {

        Route::any('/intellect/start',                 [IntellectController::class, 'start']); // Bitrix -> Admin -> Intellect
        Route::any('/intellect/save',                  [IntellectController::class, 'save']);   // Intellect -> Admin -> Bitrix
        Route::any('/intellect/get_name',              [IntellectController::class, 'get_name']);   // Intellect -> Admin 
        Route::any('/intellect/get_link',              [IntellectController::class, 'get_link']);   // Intellect -> Admin 
        Route::any('/intellect/get_time',              [IntellectController::class, 'get_time']);   // Intellect -> Admin 
        Route::any('/intellect/change_status',         [IntellectController::class, 'change_status']);   // Intellect -> Admin 
        Route::any('/intellect/send_message',          [IntellectController::class, 'send_message']);   // Admin -> Intellect 
        Route::any('/intellect/create_lead',           [IntellectController::class, 'create_lead']);   // Admin -> Intellect 
        Route::any('/intellect/test',                  [IntellectController::class, 'test']);   // Admin -> Intellect 
        Route::any('/intellect/save_quiz_after_fire',  [IntellectController::class, 'quiz_after_fire']);   // Intellect -> Admin 
        Route::any('/intellect/save_estimate_trainer', [IntellectController::class, 'save_estimate_trainer']);  


        // Bitrix -> Admin 
        Route::any('/bitrix/new-lead',     [IntellectController::class, 'newLead']);   
        Route::any('/bitrix/edit-lead',    [IntellectController::class, 'editLead']); 
        Route::any('/bitrix/edit-deal',    [IntellectController::class, 'editDeal']);  
        Route::any('/bitrix/lose-deal',    [IntellectController::class, 'loseDeal']);   
        Route::any('/bitrix/create-link',  [IntellectController::class, 'bitrixCreateLead']);  
        Route::any('/bitrix/change-resp',  [IntellectController::class, 'changeResp']);   
        Route::any('/bitrix/inhouse',      [IntellectController::class, 'inhouse']); 
        
        
        Route::group([
            'prefix' => 'statistics',
        ], function (){
            Route::any('/workdays', [KpiStatController::class, 'workdays']);
        });

    });

    

});