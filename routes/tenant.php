<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TraineeController;
use App\Http\Controllers\Admin\QuartalBonusController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\TimetrackingController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\BpartnersController;
use App\Http\Controllers\Admin\NpsController;
use App\Http\Controllers\Admin\QualityController;
use App\Http\Controllers\Admin\CheckListController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\DecompositionController;
use App\Http\Controllers\Admin\UserFineController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\GroupAnalyticsController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\TopController;
use App\Http\Controllers\Admin\FineController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\BookController;

use App\Http\Controllers\Video\VideoPlaylistController;
use App\Http\Controllers\Video\VideoCategoryController;
use App\Http\Controllers\Video\VideoGroupController;
use App\Http\Controllers\Video\VideoController;
use App\Http\Controllers\Video\VideolearningController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseResultController;
use App\Http\Controllers\KnowBaseController;
use App\Http\Controllers\UpbookController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\GroupsController;

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



Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    \Auth::routes(); 
   


    Route::get('/send-mail', function() {
        $mailData = [
            'name' => "sad sadsdads",
            'dob' => '123123131231'
        ];
        
        \Mail::to("abik50000@gmail.com")->send(new \App\Mail\SendInvitation($mailData));
    });
    

    
    Route::any('/ses', function() {
        session()->flush();
    });
    
    

    Route::any('/', [UserController::class, 'profile']);
    Route::any('/profile', [UserController::class, 'profile']); 

    Route::post('logout', [LoginController::class, 'logout']);//->name('logout');


    Route::any('/notifications/set-read/', [UserController::class, 'setNotiRead']);
    Route::any('/notifications/set-read-all/', [UserController::class, 'setNotiReadAll']);
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
    Route::get('/admin/courses/get', [CourseController::class, 'get']);
    Route::post('/admin/courses/delete', [CourseController::class, 'delete']);
    Route::post('/admin/courses/save', [CourseController::class, 'save']);
    Route::post('/admin/courses/create', [CourseController::class, 'create']);
    Route::post('/admin/courses/get-item', [CourseController::class, 'getItem']);
    Route::post('/admin/courses/upload-image', [CourseController::class, 'uploadImage']);

    Route::get('/my-courses', [MyCourseController::class, 'index']);
    Route::get('/my-courses/get', [MyCourseController::class, 'getCourses']);
    Route::get('/my-courses/get/{id}', [MyCourseController::class, 'getMyCourse']);

    Route::post('/course-results/get', [CourseResultController::class, 'get']);


    // @TODO DELETE USELESS ROUTES AND CONTROLLERS
    Route::any('/videolearning/{id?}', [VideolearningController::class, 'list'])->name('videos.playlists');
    Route::any('/videolearning/playlists/{id}', [VideolearningController::class, 'playlist'])->name('videos.playlist');
    Route::post('/videos/views', [VideolearningController::class, 'views'])->name('videos.views');

    Route::post('/setting/reset', [SettingController::class, 'reset']);

    Route::get('/playlists/get', [VideoPlaylistController::class, 'get']);
    Route::get('/playlists/get/{id}', [VideoPlaylistController::class, 'getPlaylist']);
    Route::post('/playlists/add-video', [VideoPlaylistController::class, 'add_video']);
    Route::post('/playlists/save-video', [VideoPlaylistController::class, 'save_video']);
    Route::post('/playlists/delete-video', [VideoPlaylistController::class, 'delete_video']);
    Route::post('/playlists/remove-video', [VideoPlaylistController::class, 'remove_video']);
    Route::post('/playlists/save', [VideoPlaylistController::class, 'save']);
    Route::post('/playlists/delete', [VideoPlaylistController::class, 'delete']);
    Route::post('/playlists/save-test', [VideoPlaylistController::class, 'saveTest']);
    Route::post('/playlists/add', [VideoPlaylistController::class, 'add']);
    Route::get('/video_playlists', [VideoPlaylistController::class, 'index']);

    Route::post('/playlists/delete-cat', [VideoCategoryController::class, 'delete']);
    Route::post('/playlists/add-cat', [VideoCategoryController::class, 'add']);

    Route::post('/playlists/video/update', [VideoController::class, 'updateVideo']);
    Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
    Route::post('/videos/add_comment', [VideoController::class, 'add_comment'])->name('videos.add_comment');
    Route::post('/videos/get_comment', [VideoController::class, 'get_comment'])->name('videos.get_comment');
    Route::post('/videos/upload_progress', [VideoController::class, 'upload_progress'])->name('videos.upload_progress');

    // Настройка субдомена
    Route::get('/cabinet', [CabinetController::class, 'index'] );
    Route::get('/cabinet/get', [CabinetController::class, 'get']);
    Route::post('/cabinet/save', [CabinetController::class, 'save']);

    // Книги
    Route::get('/upbooks', [UpbookController::class, 'index']);
    Route::get('/admin/upbooks', [UpbookController::class, 'edit']);
    Route::get('/admin/upbooks/get', [UpbookController::class, 'admin_get']);
    Route::post('/admin/upbooks/category/create', [UpbookController::class, 'createCategory']);
    Route::post('/admin/upbooks/category/delete', [UpbookController::class, 'deleteCategory']);
    Route::post('/admin/upbooks/tests/get', [UpbookController::class, 'getTests']);
    Route::post('/admin/upbooks/save', [UpbookController::class, 'save']);
    Route::post('/admin/upbooks/update', [UpbookController::class, 'update']);
    Route::post('/admin/upbooks/delete', [UpbookController::class, 'delete']);


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


    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/get', [PermissionController::class, 'get']);

    Route::get('/test', [TestController::class, 'test'])->name('test');
    Route::get('/wami', [TestController::class, 'send_whatsapp']);


    Route::get('/bonus', [IndexController::class, 'bonus']);
    Route::any('/bonus/update/{id}', [IndexController::class, 'bonusUpdate']);
    Route::get('/userroles', [IndexController::class, 'userroles']);
    Route::any('/userroles/update/{id}', [IndexController::class, 'userrolesUpdate']);
    Route::any('/max-session', [IndexController::class, 'maxSession']);
    Route::any('/passwords', [IndexController::class, 'passwords']);
    Route::get('/user/delete/{id}', [IndexController::class, 'deleteUser']);


    Route::post('/timetracking/settings/add/check', [CheckListController::class, 'store']); /// добавление Чек листа
    Route::get('/timetracking/settings/list/check', [CheckListController::class, 'listViewCheck']); /// список Чек листов
    Route::post('/timetracking/settings/delete/check', [CheckListController::class, 'deleteCheck']); /// удаление Чек листа по ИД
    Route::post('/timetracking/settings/edit/check', [CheckListController::class, 'editCheck']); /// Открыть  Чек лист по ИД
    Route::post('/timetracking/settings/edit/check/save/', [CheckListController::class, 'editSaveCheck']); /// Редактировать Сохранить Чек листа по ИД
    Route::post('/timetracking/settings/auth/check/user', [CheckListController::class, 'viewAuthCheck']); /// со стораны пользователя если есть будет показывать
    Route::post('/timetracking/settings/auth/check/user/send', [CheckListController::class, 'sendAuthCheck']); /// со стораны пользователя Выполнить сохр в отчет




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

    Route::post('/timetracking/kpi_save', [KpiController::class, 'saveKPI']);
    Route::post('/timetracking/kpi_get', [KpiController::class, 'getKPI']);
    Route::post('/timetracking/kpi_save_individual', [KpiController::class, 'saveKpiIndividual']);
    Route::post('/timetracking/kpi_get_individual', [KpiController::class, 'getKpiIndividual']);

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


    Route::get('/timetracking/top', [TopController::class, 'index']);
    Route::post('/timetracking/top', [TopController::class, 'fetch']);
    Route::post('/timetracking/top/save_top_value', [TopController::class, 'saveTopValue']);
    Route::post('/timetracking/top/get-rentability', [TopController::class, 'getRentability']);
    Route::post('/timetracking/top/create_gauge', [TopController::class, 'createGauge']);
    Route::post('/timetracking/top/get_activities', [TopController::class, 'getActivities']);
    Route::post('/timetracking/top/delete_gauge', [TopController::class, 'deleteGauge']);
    Route::post('/timetracking/top/save_rent_max', [TopController::class, 'saveRentMax']);
    Route::post('/timetracking/top/save_group_plan', [TopController::class, 'saveGroupPlan']);
    Route::post('/timetracking/top/top_edited_value/update', [TopController::class, 'updateTopEditedValue']);
    Route::post('/timetracking/top/proceeds/update', [TopController::class, 'updateProceeds']);


    Route::any('/timetracking/quality-control', [QualityController::class, 'index']);
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


    Route::any('/timetracking/analytics/save-call-base', [GroupAnalyticsController::class, 'saveCallBase']);
    Route::any('/timetracking/analytics', [GroupAnalyticsController::class, 'index']);
    Route::any('/timetracking/analytics/skypes/{id}', [GroupAnalyticsController::class, 'redirectToBitrixDeal']);
    Route::any('/timetracking/getanalytics', [GroupAnalyticsController::class, 'getanalytics']);
    Route::post('/timetracking/analytics/invite-users', [GroupAnalyticsController::class, 'inviteUsers']); // Приглашение стажеров
    Route::post('/timetracking/analytics/recruting/create-lead', [GroupAnalyticsController::class, 'createRecrutingLead']); // Создание лидов вручную
    Route::post('/timetracking/analytics/recruting/change-profile', [GroupAnalyticsController::class, 'changeRecruiterProfile']); // Сменить профиль рекрутера
    Route::any('/timetracking/get_kpi_totals', [GroupAnalyticsController::class, 'get_kpi_totals']);
    Route::any('/timetracking/update-settings', [GroupAnalyticsController::class, 'update']);
    Route::any('/timetracking/update-settings-extra', [GroupAnalyticsController::class, 'updateExtra']);
    Route::post('/timetracking/update-activity-total', [GroupAnalyticsController::class, 'update_activity_total']);
    Route::any('/timetracking/update-settings-individually', [GroupAnalyticsController::class, 'updateIndividually']);
    Route::get('/timetracking/analytics/activity/export', [GroupAnalyticsController::class, 'exportActivityExcel']);


    Route::any('/timetracking/an', [AnalyticsController::class, 'index']);
    Route::any('/timetracking/analytics-page/getanalytics', [AnalyticsController::class, 'get']);
    Route::get('/timetracking/analytics/activity/exportxx', [AnalyticsController::class, 'exportActivityExcel']);
    Route::post('/timetracking/analytics/add-row', [AnalyticsController::class, 'addRow']);
    Route::post('/timetracking/analytics/delete-row', [AnalyticsController::class, 'deleteRow']);
    Route::post('/timetracking/analytics/dependency/remove', [AnalyticsController::class, 'removeDependency']);
    Route::post('/timetracking/analytics/edit-stat', [AnalyticsController::class, 'editStat']);
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


    Route::get('/books/{id?}', [BpartnersController::class, 'books']);
    Route::any('/books/create/', [BpartnersController::class, 'bookscreate']);
    Route::any('/books/delete/', [BpartnersController::class, 'booksdelete']);
    Route::any('/books/order/', [BpartnersController::class, 'orderbooks']);
    Route::any('/books/rename/', [BpartnersController::class, 'renamebooks']);
    Route::any('/books/move/', [BpartnersController::class, 'movebooks']);
    Route::post('/books/get_book/', [BpartnersController::class, 'getBook']);
    Route::get('/timetracking/books', [BpartnersController::class, 'redirectToBpartnersBooks']);
    Route::get('/timetracking/kk', [BpartnersController::class, 'corp_book']);
    Route::any('/timetrakicking/kk/ajax', [BpartnersController::class, 'corp_book_ajax']);
    Route::any('/pages/update/', [BpartnersController::class, 'pagesupdate']);
    Route::any('/pages/create/', [BpartnersController::class, 'pagescreate']);
    Route::any('/pages/delete/', [BpartnersController::class, 'pagesdelete']);
    Route::any('/pages/order/', [BpartnersController::class, 'orderpages']);
    Route::any('/pages/search/', [BpartnersController::class, 'searchpages']);
    Route::any('/pages/rename/', [BpartnersController::class, 'renamepages']);
    Route::any('/page/copy/', [BpartnersController::class, 'copypages']);
    Route::any('/page/move/', [BpartnersController::class, 'movepages']);
    Route::any('/upload/images/', [BpartnersController::class, 'uploadimages']);
    Route::any('/upload/audio/', [BpartnersController::class, 'uploadaudio']);
    Route::any('/books/password/', [BpartnersController::class, 'password']);


    // Controllers with one method

    Route::post('/file/upload', [UploadController::class, 'resumableUpload']);
    Route::get('/corp_book/{id}', [LinkController::class, 'opened_corp_book']);
    Route::any('/timetracking/analytics/funnels', [LeadController::class, 'funnel_segment']);
    Route::post('/timetracking/user-fine', [UserFineController::class, 'update']);
    Route::post('/user/save/answer', [ProfileController::class, 'saveAnswer']);
    Route::post('/position/save/desc', [PositionController::class, 'savePositionDesc']);

});
