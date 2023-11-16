<?php

declare(strict_types=1);

use App\Http\Controllers as Root;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Analytics as Analytics;
use App\Http\Controllers\Api as Api;
use App\Http\Controllers\Article as Article;
use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\Company;
use App\Http\Controllers\Course as Course;
use App\Http\Controllers\Deal as Deal;
use App\Http\Controllers\Kpi as Kpi;
use App\Http\Controllers\Learning as Learning;
use App\Http\Controllers\Salary as Salary;
use App\Http\Controllers\Services as Services;
use App\Http\Controllers\Settings as Settings;
use App\Http\Controllers\Timetrack as Timetrack;
use App\Http\Controllers\Top\TopValueController;
use App\Http\Controllers\User as User;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'tenant'])->group(function () {
    Route::any('/', [User\ProfileController::class, 'newprofile']);
    Route::any('/pricing', [User\ProfileController::class, 'newprofile']);
    Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [Auth\LoginController::class, 'login']);
    Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('password/reset', [Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [Auth\ResetPasswordController::class, 'reset']);

    Route::get('/tariffs/get', [Root\Tariffs\TariffController::class, 'get']);
});

// Portal Api
Route::middleware(['web', 'tenant', 'not_admin_subdomain'])->group(function () {

    /** @author Vahagn */
    require_once __DIR__ . '/referral.php';

    Route::post('/online', [User\EmployeeController::class, 'online']);
    Route::get('/structure', [Root\Structure\StructureController::class, 'index']);

    Route::resource('work-chart', Root\WorkChart\WorkChartController::class)->except(['create', 'edit']);
    Route::group([
        'prefix' => 'work-chart',
        'as' => 'work-chart.'
    ], function () {
        Route::post('/user/add', [Root\WorkChart\UserWorkChartController::class, 'addChart']);
        Route::post('/user/delete', [Root\WorkChart\UserWorkChartController::class, 'deleteChart']);

        Route::post('/group/add', [Root\WorkChart\GroupWorkChartController::class, 'addChart']);
        Route::post('/group/delete', [Root\WorkChart\GroupWorkChartController::class, 'deleteChart']);
    });

    Route::get('/login/{subdomain}', [User\ProjectController::class, 'login']);
    Route::post('/projects/create', [User\ProjectController::class, 'create']);
    Route::get('/newprofile', [User\ProfileController::class, 'newprofile']);
    Route::get('/impersonate/{token}', function ($token) {
        return \Stancl\Tenancy\Features\UserImpersonation::makeResponse($token);
    });

    Route::middleware('auth')->get('/me', [User\UserController::class, 'me']);

    Route::group(['prefix' => 'portal', 'as' => 'portal.'], function () {
        Route::get('/current', [Root\Portal\PortalController::class, 'getCurrentPortal']);
        Route::post('/update', [Root\Portal\PortalController::class, 'update']);
    });

    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [User\ProfileController::class, 'newprofile']);
        Route::any('/personal-info', [User\ProfileController::class, 'personalInfo']);
        Route::any('/recruter-stats', [User\ProfileController::class, 'recruterStatsRates']);
        Route::any('/courses/{id}', [User\ProfileController::class, 'course']);
        Route::any('/trainee-report', [User\ProfileController::class, 'traineeReport']);
        Route::any('/payment-terms', [User\ProfileController::class, 'paymentTerms']);
        Route::get('/promotional-material', [User\ProfileController::class, 'promotionalMaterial']);
        Route::get('/referral-prsentation', [User\ProfileController::class, 'promotionalMaterial']);
        Route::put('/welcome_message', [User\ProfileController::class, 'updateWelcomeMessage']);
    });

    // Настройка субдомена
    Route::get('/cabinet', [User\CabinetController::class, 'index']);
    Route::get('/cabinet/get', [User\CabinetController::class, 'get']);
    Route::post('/cabinet/save', [User\CabinetController::class, 'save']);
    Route::any('/profile/edit/user/cart/', [User\CabinetController::class, 'editUserProfile']); ///profile save name,last_name,date ///profile save name,last_name,date
    Route::post('/profile/remove/card/', [User\CabinetController::class, 'removeCardProfile']); ///удаление карты индивидуально
    Route::post('/profile/save-cropped-image', [User\CabinetController::class, 'uploadCroppedImageProfile']); /// загрузка аватарки vue внутри profile
    Route::post('/profile/access', [Admin\AccessController::class, 'switchAccess']);

    ///Настройка профайл
    Route::post('/profile/upload/image/profile/', [User\EmployeeController::class, 'uploadImageProfile']); /// загрузка обрезаной аватарки vue внутри profile
    Route::any('/profile/upload/edit/', [User\EmployeeController::class, 'uploadPhoto'])->name('uploadPhoto'); /// загрузка аватарки со стороны Blade javascript
    Route::post('/profile/country/city/', [User\EmployeeController::class, 'searchCountry']); /// поиск городов через Профиль
    Route::post('/timetracking/user-fine', [User\FineController::class, 'update']);

    Route::any('/bonuses', [User\ProfileController::class, 'getBonuses']);
    Route::post('/corp_book/set-read/', [User\EmployeeController::class, 'corp_book_read']); // Прочитать страницу из корп книги @TODO при назначении книги
    Route::any('/timetracking/user/{id}', [User\EmployeeController::class, 'profile']);
    Route::any('/timetracking/get-persons', [User\EmployeeController::class, 'getpersons']);
    Route::any('/timetracking/get-persons-testing', [User\EmployeeController::class, 'newGetPersons']);
//    Route::resource('timetracking/work-chart',Settings\WorkChart\WorkChartController::class);
    Route::get('/timetracking/create-person', [User\EmployeeController::class, 'createPerson'])->name('users.create');
    Route::post('/timetracking/person/store', [Settings\UserController::class, 'store'])->name('users.store');
    Route::get('/timetracking/edit-person', [User\EmployeeController::class, 'editperson'])->name('users.edit');
    Route::post('/timetracking/person/update', [Settings\UserController::class, 'update'])->name('users.update');
    Route::post('/timetracking/person/update-new', [Settings\UserController::class, 'update']);
    Route::post('/timetracking/edit-person/group', [User\EmployeeController::class, 'editPersonGroup']); // Удалять добавлять пользователя в группы
    Route::post('/timetracking/edit-person/head_in_groups', [User\EmployeeController::class, 'setUserHeadInGroups']); // Удалять добавлять пользователя руководителем групп
    Route::any('/timetracking/delete-person', [User\EmployeeController::class, 'deleteUser'])->name('removeUser');
    Route::any('/timetracking/recover-person', [User\EmployeeController::class, 'recoverUser'])->name('recoverUser');

    // Самостоятельная отметка стажеров
    Route::get('/autocheck/{id}', [User\TraineeController::class, 'autocheck']); // cтраница со ссылками для отметки стажерами
    Route::post('/autocheck/{id}', [User\TraineeController::class, 'save']); //
    Route::get('/autochecker', [User\TraineeController::class, 'autochecker']);  // включить возможность отметки
    Route::post('/autochecker/{id}', [User\TraineeController::class, 'open']);  // включить возможность отметки

    // pages
    Route::any('/efd', [Root\PageController::class, 'estimate_first_day']);  // анкета
    Route::get('/login-as-employee/{id}', [Root\PageController::class, 'loginAs']); // войти через test  на страницу профиля
    Route::get('/quiz_after_fire', [Root\PageController::class, 'quiz_after_fire']); // анкета
    Route::get('/estimate_trainer', [Root\PageController::class, 'estimate_trainer']);  // анкета
    Route::get('/test', [Root\TestController::class, 'test'])->name('test');
    Route::get('/test2', [Root\TestController::class, 'test2'])->name('test2');

    // courses
    Route::group([
        'prefix' => 'course',
        'as' => 'course.'
    ], function () {
        Route::post('/regress', [Course\RegressCourseController::class, 'regress']);
        Route::get('/progress', [Course\CourseProgressController::class, 'progress']);
    });

    Route::get('/courses', [Course\CourseController::class, 'index']);
    Route::post('/courses/save-order', [Course\CourseController::class, 'saveOrder']);
    Route::get('/admin/courses/get', [Course\CourseController::class, 'get']);
    Route::post('/admin/courses/delete', [Course\CourseController::class, 'delete']);
    Route::post('/admin/courses/save', [Course\CourseController::class, 'save']);
    Route::post('/admin/courses/create', [Course\CourseController::class, 'create']);
    Route::post('/admin/courses/get-item', [Course\CourseController::class, 'getItem']);
    Route::post('/admin/courses/upload-image', [Course\CourseController::class, 'uploadImage']);

    Route::get('/my-courses', [Course\MyCourseController::class, 'index']);
    Route::get('/my-courses/get', [Course\MyCourseController::class, 'getCourses']);
    Route::get('/my-courses/get/{id}', [Course\MyCourseController::class, 'getMyCourse']);
    Route::post('/my-courses/pass', [Course\MyCourseController::class, 'pass']);

    Route::post('/course-results/get', [Course\CourseResultController::class, 'get']);
    Route::post('/course-results/nullify', [Course\CourseResultController::class, 'nullify']);

    Route::get('course/item-result', [Course\CourseResultController::class, 'getCourseItemAndResult']);
    // glossary
    Route::get('/glossary/get', [Learning\GlossaryController::class, 'get']);
    Route::post('/glossary/save', [Learning\GlossaryController::class, 'save']);
    Route::post('/glossary/update-access', [Learning\GlossaryController::class, 'updateAccess']);
    Route::post('/glossary/get-access', [Learning\GlossaryController::class, 'getAccess']);
    Route::post('/glossary/delete', [Learning\GlossaryController::class, 'delete']);

    Route::get('/learning/books', [Learning\LearningController::class, 'books']);
    Route::get('/learning/videos', [Learning\LearningController::class, 'videos']);

    Route::post('/file/upload', [Learning\FileUploadController::class, 'uploadLargeFiles'])->name('files.upload.large');
    Route::get('/corp_book/{id}', [Learning\LinkController::class, 'opened_corp_book']);

    // Video
    Route::get('/playlists/get', [Learning\Video\VideoPlaylistController::class, 'get']);
    Route::post('/playlists/video', [Learning\Video\VideoPlaylistController::class, 'getVideo']);
    Route::post('/playlists/get', [Learning\Video\VideoPlaylistController::class, 'getPlaylist']);
    Route::post('/playlists/add-video', [Learning\Video\VideoPlaylistController::class, 'add_video']);
    Route::post('/playlists/save-video', [Learning\Video\VideoPlaylistController::class, 'save_video']);
    Route::post('/playlists/save-video-fast', [Learning\Video\VideoPlaylistController::class, 'save_video_fast']);
    Route::post('/playlists/delete-video', [Learning\Video\VideoPlaylistController::class, 'delete_video']);
    Route::post('/playlists/remove-video', [Learning\Video\VideoPlaylistController::class, 'remove_video']);
    Route::post('/playlists/save', [Learning\Video\VideoPlaylistController::class, 'save']);
    Route::post('/playlists/save-fast', [Learning\Video\VideoPlaylistController::class, 'saveFast']);
    Route::post('/playlists/delete', [Learning\Video\VideoPlaylistController::class, 'delete']);
    Route::post('/playlists/save-test', [Learning\Video\VideoPlaylistController::class, 'saveTest']);
    Route::post('/playlists/add', [Learning\Video\VideoPlaylistController::class, 'add']);
    Route::get('/video_playlists', [Learning\Video\VideoPlaylistController::class, 'index']);
    Route::get('/video_playlists/{category}/{playlist}', [Learning\Video\VideoPlaylistController::class, 'saveIndex']);
    Route::get('/video_playlists/{category}/{playlist}/{video}', [Learning\Video\VideoPlaylistController::class, 'saveIndexVideo']);
    Route::post('/playlists/groups/create', [Learning\Video\VideoGroupController::class, 'create']);
    Route::post('/playlists/groups/save', [Learning\Video\VideoGroupController::class, 'save']);
    Route::post('/playlists/groups/delete', [Learning\Video\VideoGroupController::class, 'delete']);
    Route::post('/playlists/delete-cat', [Learning\Video\VideoCategoryController::class, 'delete']);
    Route::post('/playlists/add-cat', [Learning\Video\VideoCategoryController::class, 'add']);
    Route::post('/playlists/save-cat', [Learning\Video\VideoCategoryController::class, 'save']);
    Route::post('/playlists/video/update', [Learning\Video\VideoController::class, 'updateVideo']);
    Route::post('/videos/upload', [Learning\Video\VideoController::class, 'upload'])->name('videos.upload');
    Route::post('/videos/save-order', [Learning\Video\VideoPlaylistController::class, 'saveOrder']);
    Route::post('/videos/get-playlists-to-move', [Learning\Video\VideoPlaylistController::class, 'getPlaylistsToMove']);
    Route::post('/videos/move-to-playlist', [Learning\Video\VideoPlaylistController::class, 'moveToPlaylist']);
    Route::post('/videos/add_comment', [Learning\Video\VideoController::class, 'add_comment'])->name('videos.add_comment');
    Route::post('/videos/get_comment', [Learning\Video\VideoController::class, 'get_comment'])->name('videos.get_comment');
    Route::post('/videos/upload_progress', [Learning\Video\VideoController::class, 'upload_progress'])->name('videos.upload_progress');
    Route::post('/playlists/delete-question', [Learning\Video\VideoPlaylistController::class, 'deleteQuestion']);

    // Книги
    Route::get('/admin/upbooks', [Learning\UpbookController::class, 'index']);
    Route::post('/upbooks/save-cat', [Learning\UpbookController::class, 'saveCat']);
    Route::get('/admin/upbooks/get', [Learning\UpbookController::class, 'admin_get']);
    Route::post('/admin/upbooks/category/create', [Learning\UpbookController::class, 'createCategory']);
    Route::post('/admin/upbooks/category/delete', [Learning\UpbookController::class, 'deleteCategory']);
    Route::post('/admin/upbooks/segments/get', [Learning\UpbookController::class, 'getSegments']);
    Route::post('/admin/upbooks/save', [Learning\UpbookController::class, 'save']);
    Route::post('/admin/upbooks/update', [Learning\UpbookController::class, 'update']);
    Route::post('/admin/upbooks/delete', [Learning\UpbookController::class, 'delete']);
    Route::post('/admin/upbooks/segments/save', [Learning\UpbookController::class, 'saveSegment']);
    Route::post('/admin/upbooks/segments/delete', [Learning\UpbookController::class, 'deleteSegment']);

    // База знаний
    Route::get('/kb', [Learning\KnowBaseController::class, 'index']);
    Route::get('/kb/get', [Learning\KnowBaseController::class, 'get']);
    Route::post('/kb/get', [Learning\KnowBaseController::class, 'getPage']);
    Route::post('/kb/search', [Learning\KnowBaseController::class, 'search']);
    Route::get('/kb/get-archived', [Learning\KnowBaseController::class, 'getArchived']);
    Route::post('/kb/tree', [Learning\KnowBaseController::class, 'getTree']);
    Route::post('/kb/page/update', [Learning\KnowBaseController::class, 'updatePage']);
    Route::post('/kb/page/delete-section', [Learning\KnowBaseController::class, 'deleteSection']);
    Route::post('/kb/page/restore-section', [Learning\KnowBaseController::class, 'restoreSection']);
    Route::post('/kb/page/add-section', [Learning\KnowBaseController::class, 'addSection']);
    Route::post('/kb/page/save-order', [Learning\KnowBaseController::class, 'saveOrder']);
    Route::post('/kb/page/save-test', [Learning\KnowBaseController::class, 'saveTest']);
    Route::post('/kb/page/create', [Learning\KnowBaseController::class, 'createPage']);
    Route::post('/kb/page/delete', [Learning\KnowBaseController::class, 'deletePage']);
    Route::post('/kb/page/update-section', [Learning\KnowBaseController::class, 'updateSection']);
    Route::post('/kb/page/get-access', [Learning\KnowBaseController::class, 'getAccess']);
    Route::get('/kb/get-settings', [Learning\KnowBaseController::class, 'getSettings']);
    Route::post('/kb/save-settings', [Learning\KnowBaseController::class, 'saveSettings']);

    // Settings
    Route::post('/setting/reset', [Settings\OtherSettingController::class, 'reset']);
    Route::post('/settings/get/', [Settings\OtherSettingController::class, 'getSettings']);
    Route::post('/settings/save', [Settings\OtherSettingController::class, 'saveSettings']);

    // Permissions
    Route::get('/permissions', [Settings\PermissionController::class, 'index']);
    Route::get('/permissions/get', [Settings\PermissionController::class, 'get']);
    Route::post('/permissions/create-role', [Settings\PermissionController::class, 'createRole']);
    Route::post('/permissions/update-role', [Settings\PermissionController::class, 'updateRole']);
    Route::post('/permissions/update-target', [Settings\PermissionController::class, 'updateTarget']);
    Route::post('/permissions/delete-target', [Settings\PermissionController::class, 'deleteTarget']);
    Route::post('/permissions/delete-role', [Settings\PermissionController::class, 'deleteRole']);

    // positions
    Route::post('/timetracking/settings/positions/delete', [Settings\PositionController::class, 'destroy']);

    // штрафы
    Route::get('/timetracking/fine', [Settings\FineController::class, 'index']);
    Route::put('/timetracking/fine', [Settings\FineController::class, 'update']);


    // Типы наград для сотрудников
    Route::group(['prefix' => 'award-categories', 'as' => 'award-categories.'], function () {
        Route::get('/get', [Settings\Award\AwardCategoryController::class, 'index'])->name('get');
        Route::get('/get/{awardCategory}', [Settings\Award\AwardCategoryController::class, 'show'])->name('show');
        Route::get('/get/awards/{awardCategory}', [Settings\Award\AwardCategoryController::class, 'categoryAwards'])->name('awards');
        Route::post('/store', [Settings\Award\AwardCategoryController::class, 'store'])->name('store');
        Route::put('/update/{awardCategory}', [Settings\Award\AwardCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{awardCategory}', [Settings\Award\AwardCategoryController::class, 'destroy'])->name('destroy');
    });

    // Награды для сотрудников
    Route::group(['prefix' => 'awards', 'as' => 'awards.', 'middleware' => 'auth'], function () {
        Route::post('/reward', [Settings\Award\AwardController::class, 'reward'])->name('reward');
        Route::delete('/reward-delete', [Settings\Award\AwardController::class, 'deleteReward'])->name('delete-reward');
        Route::get('/my', [Settings\Award\AwardController::class, 'myAwards'])->name('my-awards');
        Route::get('/course', [Settings\Award\AwardController::class, 'courseAward'])->name('course-awards');
        Route::get('/courses', [Settings\Award\AwardController::class, 'coursesAward'])->name('courses-awards');
        Route::post('/courses/store/{award}', [Settings\Award\AwardController::class, 'storeCoursesAward'])->name('courses-awards-store');
        Route::get('/type', [Settings\Award\AwardController::class, 'awardsByType'])->name('type-awards');
        Route::get('/read', [Settings\Award\AwardController::class, 'read'])->name('read-awards');
        Route::get('/get', [Settings\Award\AwardController::class, 'index'])->middleware('is_admin')->name('get');
        Route::post('/store', [Settings\Award\AwardController::class, 'store'])->name('store');
        Route::put('/update/{award}', [Settings\Award\AwardController::class, 'update'])->middleware('is_admin')->name('update');
        Route::delete('/delete/{award}', [Settings\Award\AwardController::class, 'destroy'])->name('destroy');
        Route::get('/download/{award}', [Settings\Award\AwardController::class, 'downloadFile'])->name('downloadFile');
        Route::get('/fix-preview', [Settings\Award\AwardController::class, 'fixPreviewPage']);
        Route::post('/add-preview', [Settings\Award\AwardController::class, 'addPreview'])->name('add-preview');
        Route::post('/add-preview-second', [Settings\Award\AwardController::class, 'addPreviewSecond'])->name('add-preview-second');

    });

    Route::post('/checklist/tasks', [Settings\CheckListController::class, 'getTasks']);
    Route::post('/checklist/save', [Settings\CheckListController::class, 'saveTasks']);
    Route::post('/checklist/get-checklist-by-user', [Settings\CheckListController::class, 'getChecklistByUser']);
    Route::post('/checklist/save-checklist', [Settings\CheckListController::class, 'saveChecklist']);

    Route::post('/timetracking/settings/add/check', [Settings\CheckListController::class, 'store']); /// добавление Чек листа
    Route::get('/timetracking/settings/list/check', [Settings\CheckListController::class, 'listViewCheck']); /// список Чек листов
    Route::post('/timetracking/settings/delete/check', [Settings\CheckListController::class, 'deleteCheck']); /// удаление Чек листа по ИД
    Route::post('/timetracking/settings/edit/check', [Settings\CheckListController::class, 'editCheck']); /// Открыть  Чек лист по ИД

    Route::get('/timetracking/settings/auth/check/user', [Settings\CheckListController::class, 'viewAuthCheck']); /// со стораны пользователя если есть будет показывать
    Route::post('/timetracking/settings/auth/check/user/send', [Settings\CheckListController::class, 'sendAuthCheck']); /// со стораны пользователя Выполнить сохр в отчет
    Route::post('/timetracking/settings/auth/check/user/responsibility', [Settings\CheckListController::class, 'responsibility']); ///   Добавить ответственного лица
    Route::post('/timetracking/settings/get/modal/', [Settings\CheckListController::class, 'getModal']); ///   Получить пользователей
    Route::post('/timetracking/settings/auth/check/search/selected', [Settings\CheckListController::class, 'searchSelected']);

    Route::post('/timetracking/settings/edit/check/save/', [Settings\CheckListController::class, 'editSaveCheck']); /// Редактировать Сохранить Чек листа по ИД

    Route::get('/superselect/get', [Settings\PermissionController::class, 'superselect']);
    Route::get('/superselect/get-alt', [Settings\PermissionController::class, 'superselectAlt']);

    // tt
    Route::any('/timetracking', [Timetrack\TimetrackingController::class, 'index']);
    Route::any('/timetracking/fines', [Timetrack\TimetrackingController::class, 'fines']);
    Route::any('/timetracking/info', [Timetrack\TimetrackingController::class, 'info']);
    Route::any('/timetracking/set-day', [Timetrack\TimetrackingController::class, 'setDay']);
    Route::any('/timetracking/history', [Timetrack\TimetrackingController::class, 'getHistory']);

    Route::post('timetracking/settings/get_time_addresses', [Timetrack\TimetrackingController::class, 'getTimeAddresses']);
    Route::post('timetracking/settings/save_time_addresses', [Timetrack\TimetrackingController::class, 'saveTimeAddresses']);

    Route::any('/timetracking/user/save', [Timetrack\TimetrackingController::class, 'saveprofile']);
    Route::post('/timetracking/settings/notifications/update', [Timetrack\TimetrackingController::class, 'updateNotificationTemplate']);
    Route::get('/timetracking/settings/notifications/get', [Timetrack\TimetrackingController::class, 'getNotificationTemplates']);
    Route::post('/timetracking/settings/notifications/user', [Timetrack\TimetrackingController::class, 'getUserNotifications']);
    Route::post('/timetracking/settings/notifications/user/save', [Timetrack\TimetrackingController::class, 'saveUserNotifications']);
    Route::get('/timetracking/reports', [Timetrack\TimetrackingController::class, 'reports']);
    Route::post('/timetracking/reports', [Timetrack\TimetrackingController::class, 'getReports']);
    Route::post('/timetracking/reports/update/day', [Timetrack\TimetrackingController::class, 'updateTimetrackingDay']);
    Route::any('/timetracking/starttracking', [Timetrack\TimetrackingController::class, 'timetracking']);
    Route::any('/timetracking/status', [Timetrack\TimetrackingController::class, 'trackerstatus']);

    Route::post('/timetracking/overtime', [Timetrack\TimetrackingController::class, 'overtime']);
    Route::get('/timetracking/overtime/accept', [Timetrack\TimetrackingController::class, 'accept']);
    Route::get('/timetracking/overtime/reject', [Timetrack\TimetrackingController::class, 'reject']);

    Route::any('/timetracking/zarplata-table', [Timetrack\TimetrackingController::class, 'zarplatatable']);
    Route::any('/timetracking/zarplata-table-new', [Timetrack\TimetrackingController::class, 'zarplatatableNew']);
    Route::post('/timetracking/salary-balance', [Timetrack\SalaryWorkChartController::class, 'salaryBalance']);
    Route::post('/order-persons-to-group', [Timetrack\TimetrackingController::class, 'orderPersonsToGroup']); // Заказ сотрудников в группы для Руководителей
    Route::post('/timetracking/apply-person', [Timetrack\TimetrackingController::class, 'applyPerson']); // Принятие на штат стажера
    Route::post('/timetracking/get-totals-of-reports', [Timetrack\TimetrackingController::class, 'getTotalsOfReports']);

    Route::group(['prefix' => 'group-user'], function () {
        Route::post('/save', [Timetrack\TimetrackingController::class, 'addUsers']);
        Route::post('/drop', [Timetrack\TimetrackingController::class, 'dropUsers']);
    });

    Route::any('/timetracking/reports/add-editors', [Timetrack\TimetrackingController::class, 'usereditreports']);
    Route::any('/timetracking/reports/get-editors', [Timetrack\TimetrackingController::class, 'modalcheckuserrole']);
    Route::any('/timetracking/reports/check-user', [Timetrack\TimetrackingController::class, 'checkuserrole']);


    #==================================
    // REPLACES @TODO
//    Route::post('/timetracking/reports/enter-report/setmanual', [Timetrack\TimetrackingController::class, 'enterreportManually']);
    Route::post('/timetracking/reports/enter-report/setmanual', [Timetrack\EnterReportController::class, 'manually']);

    Route::any('/timetracking/reports/enter-report', [Timetrack\TimetrackingController::class, 'enterreport']);
    Route::any('/timetracking/reports/enter-report-post', [Timetrack\EnterReportController::class, 'enter']);

    Route::any('/timetracking/group/save', [Timetrack\TimetrackingController::class, 'savegroup']);
    Route::post('/timetracking/group/save-new', [Settings\Group\GroupController::class, 'store']);

    Route::any('/timetracking/group/delete', [Timetrack\TimetrackingController::class, 'deletegroup']);
    Route::post('/timetracking/group/delete-new', [Settings\Group\GroupController::class, 'deactivate']);

    Route::any('/timetracking/groups', [Timetrack\TimetrackingController::class, 'getgroups']);
    Route::get('/timetracking/groups-new', [Settings\Group\GroupController::class, 'get']);

    Route::post('/timetracking/groups/restore', [Timetrack\TimetrackingController::class, 'restoreGroup']);
    Route::post('/timetracking/groups/restore-new', [Settings\Group\GroupController::class, 'restore']);

    Route::any('/timetracking/users', [Timetrack\TimetrackingController::class, 'getusersgroup']);
    Route::any('/timetracking/users-new', [Settings\Group\GroupUserController::class, 'get']);

    Route::any('/timetracking/users/group/save', [Timetrack\TimetrackingController::class, 'saveusersgroup']);
    Route::any('/timetracking/users/group/save-new', [Settings\Group\GroupUserController::class, 'save']);

    Route::any('/timetracking/settings/positions', [Timetrack\TimetrackingController::class, 'positions']);
    Route::any('/timetracking/settings/positions-new', [Settings\PositionController::class, 'all']);

    Route::any('/timetracking/settings/positions/get', [Timetrack\TimetrackingController::class, 'getPosition']);
    Route::post('/timetracking/settings/positions/get-new', [Settings\PositionController::class, 'get']);

    Route::any('/timetracking/settings/positions/save', [Timetrack\TimetrackingController::class, 'savePositions']);
    Route::any('/timetracking/settings/positions/save-new', [Settings\PositionController::class, 'savePositionWithDescription']);

    Route::post('/timetracking/settings/positions/add', [Timetrack\TimetrackingController::class, 'addPosition']);
    Route::post('/timetracking/settings/positions/add-new', [Settings\PositionController::class, 'store']);

    Route::any('/timetracking/settings', [Timetrack\TimetrackingController::class, 'settings']);
    Route::any('/timetracking/settings-new', [Settings\SettingController::class, 'setting']);

    Route::any('/timetracking/settings/add', [Timetrack\TimetrackingController::class, 'addsettings']);
    Route::post('/timetracking/settings/add-new', [Settings\SettingController::class, 'create']);

    Route::any('/timetracking/settings/delete', [Timetrack\TimetrackingController::class, 'deletesettings']);
    Route::delete('/timetracking/settings/delete-new', [Settings\SettingController::class, 'delete']);

    #==================================

    // salaries
    Route::get('/timetracking/salaries', [Salary\SalaryController::class, 'index']);
    Route::get('/timetracking/salaries/export', [Salary\SalaryController::class, 'exportExcel']);
    Route::post('/timetracking/salaries/get-total', [Salary\SalaryController::class, 'getTotal']);
    Route::post('/timetracking/salaries', [Salary\SalaryController::class, 'salaries']);
    Route::post('/timetracking/salaries/update', [Salary\SalaryController::class, 'update']);
    Route::post('/timetracking/salaries/recalc', [Salary\SalaryController::class, 'recalc']);
    Route::post('/timetracking/salaries/edit-premium-old', [Salary\SalaryController::class, 'editPremium']);
    Route::post('/timetracking/salaries/edit-premium', [Salary\PremiumController::class, 'edit']);
    Route::post('/timetracking/salaries/approve-salary', [Salary\SalaryController::class, 'approveSalary']);
    Route::post('/timetracking/salaries/bonuses', [Salary\SalaryController::class, 'bonuses']);
    Route::post('/timetracking/salaries/advances', [Salary\SalaryController::class, 'advances'])->name('advances');
    Route::post('/timetracking/salaries/fines', [Salary\SalaryController::class, 'fines'])->name('fines');
    Route::post('/timetracking/salaries/taxes', [Salary\SalaryController::class, 'taxes'])->name('taxes');
    Route::post('/profile/salary/get', [Salary\ProfileSalaryController::class, 'get']);
    Route::post('/timetracking/quarter/store', [Salary\QuartalBonusController::class, 'storePersonQuartal']); /// добавление квартала
    Route::post('/timetracking/quarter/delete', [Salary\QuartalBonusController::class, 'deleteQuartal']); /// удаление квартала
    Route::post('/timetracking/quarter/get/quarter/', [Salary\QuartalBonusController::class, 'getQuartalBonuses']);

    // Quality control
    Route::any('/timetracking/quality-control/', [Analytics\QualityController::class, 'index']);
    Route::any('/timetracking/quality-control/export', [Analytics\QualityController::class, 'exportExcel']);
    Route::any('/timetracking/quality-control/change-type', [Analytics\QualityController::class, 'changeType']);
    Route::any('/timetracking/quality-control/exportall', [Analytics\QualityController::class, 'exportAllExcel']);
    Route::post('/timetracking/quality-control/save', [Analytics\QualityController::class, 'saveRecord']);
    Route::post('/timetracking/quality-control/saveweekly', [Analytics\QualityController::class, 'saveWeeklyRecord']);
    Route::post('/timetracking/quality-control/delete', [Analytics\QualityController::class, 'deleteRecord']);
    Route::post('/timetracking/quality-control/records', [Analytics\QualityController::class, 'getRecords']);
    Route::post('/timetracking/quality-control/crits/save', [Analytics\QualityController::class, 'saveCrits']);

    // ???? import hours in timetracking ?
    Route::post('/timetracking/settings/groups/importexcel', [Analytics\GroupsController::class, 'import']);
    Route::post('/timetracking/settings/groups/importexcel/save', [Analytics\GroupsController::class, 'saveTimes']);
    Route::any('/timetracking/users/bonus/save', [Analytics\GroupsController::class, 'saveBonuses']);

    // Import active
    Route::post('/timetracking/analytics/activity/importexcel', [Analytics\ActivityController::class, 'import']);
    Route::post('/timetracking/analytics/activity/importexcel/save', [Analytics\ActivityController::class, 'saveTimes']);

    // decomposition
    Route::post('/timetracking/analytics/decomposition/save', [Analytics\DecompositionController::class, 'save']);
    Route::delete('/timetracking/analytics/decomposition/delete', [Analytics\DecompositionController::class, 'delete']);

    // nps
    Route::any('/estimate_your_trainer', [Analytics\NpsController::class, 'estimate_your_trainer']); // анкета
    Route::get('/timetracking/nps', [Analytics\NpsController::class, 'index']);
    Route::post('/timetracking/nps', [Analytics\NpsController::class, 'fetch']);

    // TOP
    Route::get('/timetracking/top', [Analytics\TopController::class, 'index']);
    Route::post('/timetracking/top', [Analytics\TopController::class, 'fetch']);
    Route::post('/timetracking/top/save_top_value', [Analytics\TopController::class, 'saveTopValue']);
    Route::post('/timetracking/top/get-rentability', [Analytics\TopController::class, 'getRentability']);
    Route::post('/timetracking/top/get-rentability-on-month', [Analytics\TopController::class, 'getRentabilityOnMonth']);
    Route::post('/timetracking/top/create_gauge', [Analytics\TopController::class, 'createGauge']);
    Route::post('/timetracking/top/get_activities', [Analytics\TopController::class, 'getActivities']);
    Route::post('/timetracking/top/delete_gauge', [Analytics\TopController::class, 'deleteGauge']);
    Route::post('/timetracking/top/save_rent_max', [Analytics\TopController::class, 'saveRentMax']);
    Route::post('/timetracking/top/save_group_plan', [Analytics\TopController::class, 'saveGroupPlan']);
    Route::post('/timetracking/top/top_edited_value/update', [Analytics\TopController::class, 'updateTopEditedValue']);
    Route::post('/timetracking/top/proceeds/update', [Analytics\TopController::class, 'updateProceeds']);

    Route::post('/top/utility-archive', [TopValueController::class, 'archiveUtility']);
    Route::get('/top/utility/list', [TopValueController::class, 'listUtility']);
    Route::get('/top/rentability/list', [TopValueController::class, 'listRentability']);
    Route::get('/top/proceeds/list', [TopValueController::class, 'listProceeds']);
    Route::post('/top/switch', [TopValueController::class, 'switch']);

    // HR analytics
    Route::any('/timetracking/analytics/save-call-base', [Analytics\HrController::class, 'saveCallBase']);
    Route::any('/timetracking/analytics', [Analytics\HrController::class, 'index']);
    Route::any('/timetracking/analytics/skypes/{id}', [Analytics\HrController::class, 'redirectToBitrixDeal']);

    //TODO - разбить на несколько запросов
    Route::group(['prefix' => '/timetracking/getanalytics'], function () {
        Route::any('', [Analytics\HrController::class, 'recrutmentAnalytics']);
        Route::any('/recruitment-statictics', [Analytics\HrController::class, 'getRecruitmentStatictics']);
        Route::any('/synoptics', [Analytics\HrController::class, 'getSynoptics']);
        Route::any('/internship-second-stage', [Analytics\HrController::class, 'getInternshipSecondStage']);
        Route::any('/trainees', [Analytics\HrController::class, 'getTrainees']);
        Route::any('/funnel', [Analytics\HrController::class, 'getFunnel']);
        Route::any('/dismiss', [Analytics\HrController::class, 'getDismissStatistics']);
    });

    Route::post('/get/deals',[Analytics\HrController::class,'createDeal'])->name('create.deal');

    Route::any('/timetracking/analytics/invite-users', [Analytics\HrController::class, 'inviteUsers']); // Приглашение стажеров
    Route::post('/timetracking/analytics/recruting/create-lead', [Analytics\HrController::class, 'createRecrutingLead']); // Создание лидов вручную
    Route::post('/timetracking/analytics/recruting/change-profile', [Analytics\HrController::class, 'changeRecruiterProfile']); // Сменить профиль рекрутера
    Route::any('/timetracking/get_kpi_totals', [Analytics\HrController::class, 'get_kpi_totals']);
    Route::any('/timetracking/update-settings', [Analytics\HrController::class, 'update']);
    Route::post('/timetracking/update-activity-total', [Analytics\HrController::class, 'update_activity_total']);
    Route::any('/timetracking/update-settings-individually', [Analytics\HrController::class, 'updateIndividually']);
    Route::get('/timetracking/analytics/activity/export', [Analytics\HrController::class, 'exportActivityExcel']);
    Route::get('/hr/ref-links', [Analytics\HrController::class, 'getRefLinks']);
    Route::post('/hr/ref-links/save', [Analytics\HrController::class, 'saveRefLinks']);
    Route::post('/timetracking/getactivetrainees', [Analytics\HrController::class, 'getActiveTrainees']);

    // analytics
    Route::any('/timetracking/an', [Analytics\AnalyticsController::class, 'index']);
    Route::any('/timetracking/analytics-page/getanalytics', [Analytics\AnalyticsController::class, 'get']);

    Route::get('/timetracking/analytics/activity/exportxx', [Analytics\AnalyticsController::class, 'exportActivityExcel']);
    Route::post('/timetracking/analytics/add-row', [Analytics\AnalyticsController::class, 'addRow']);
    Route::post('/timetracking/analytics/delete-row', [Analytics\AnalyticsController::class, 'deleteRow']);
    Route::post('/timetracking/analytics/dependency/remove', [Analytics\AnalyticsController::class, 'removeDependency']);
    Route::post('/timetracking/analytics/edit-stat', [Analytics\AnalyticsController::class, 'editStat']);
    Route::post('/timetracking/analytics/set-decimals', [Analytics\AnalyticsController::class, 'setDecimals']);
    Route::post('/timetracking/analytics/new-group', [Analytics\AnalyticsController::class, 'newGroup']);
    Route::post('/timetracking/analytics/create-activity', [Analytics\AnalyticsController::class, 'createActivity']);
    Route::post('/timetracking/analytics/edit-activity', [Analytics\AnalyticsController::class, 'editActivity']);
    Route::post('/timetracking/analytics/update-stat', [Analytics\AnalyticsController::class, 'updateUserStat']);
    Route::get('/timetracking/analytics/activity/removed/users/{id}', [Analytics\AnalyticsController::class, 'getRemovedUsers']);
    Route::post('/timetracking/analytics/activity/remove/group', [Analytics\AnalyticsController::class, 'removeActivityGroup']);

    Route::post('/timetracking/analytics/save-cell-activity', [Analytics\AnalyticsController::class, 'saveCellActivity']);
    Route::post('/timetracking/analytics/save-cell-activity-new', [Analytics\CellController::class, 'saveCellActivity']);

    Route::post('/timetracking/analytics/save-cell-time', [Analytics\AnalyticsController::class, 'saveCellTime']);
    Route::post('/timetracking/analytics/save-cell-time-new', [Analytics\CellController::class, 'saveCellTime']);

    Route::post('/timetracking/analytics/save-cell-sum', [Analytics\AnalyticsController::class, 'saveCellSum']);
    Route::post('/timetracking/analytics/save-cell-sum-new', [Analytics\CellController::class, 'saveCellSum']);

    Route::post('/timetracking/analytics/save-cell-avg', [Analytics\AnalyticsController::class, 'saveCellAvg']);
    Route::post('/timetracking/analytics/save-cell-avg-new', [Analytics\CellController::class, 'saveCellAvg']);

    Route::post('/timetracking/analytics/change_order', [Analytics\AnalyticsController::class, 'change_order']);
    Route::post('/timetracking/analytics/delete_activity', [Analytics\AnalyticsController::class, 'delete_activity']);
    Route::post('/timetracking/analytics/add-depend', [Analytics\AnalyticsController::class, 'add_depend']);
    Route::post('/timetracking/analytics/archive_analytics', [Analytics\AnalyticsController::class, 'archive_analytics']);
    Route::post('/timetracking/analytics/restore_analytics', [Analytics\AnalyticsController::class, 'restore_analytics']);
    Route::post('/timetracking/analytics/add-formula-1-31', [Analytics\AnalyticsController::class, 'addFormula_1_31']);
    Route::post('/timetracking/analytics/add-remote-inhouse', [Analytics\AnalyticsController::class, 'addRemoteInhouse']);
    Route::post('/timetracking/analytics/add-salary', [Analytics\AnalyticsController::class, 'addSalary']);
    Route::any('/timetracking/user-statistics-by-month', [Analytics\AnalyticsController::class, 'getUserStatisticsByMonth']);

    Route::prefix('v2/analytics-page')
        ->group(base_path('routes/v2-analytics-routes.php'))
        ->middleware(['tenant']);

    // Редактирование бонусов
    Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function () {
        Route::post('get', [Kpi\BonusController::class, 'get'])->name('get');
        Route::post('user', [Kpi\BonusController::class, 'getUserBonuses'])->name('getUserBonuses');
        Route::post('save', [Kpi\BonusController::class, 'save'])->name('save');
        Route::put('update', [Kpi\BonusController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [Kpi\BonusController::class, 'delete'])->name('delete');
        Route::post('/set/status', [Kpi\KpiBonusStatusController::class, 'setActive']);
        Route::put('read', [Kpi\BonusController::class, 'read'])->name('read');
    });

    // Редактирование квартальной премии
    Route::group(['prefix' => 'quartal-premiums'], function () {
        Route::post('get', [Kpi\QuartalPremiumController::class, 'get'])->name('quartal-premium.get');
        Route::post('save', [Kpi\QuartalPremiumController::class, 'save'])->name('quartal-premium.save');
        Route::put('update', [Kpi\QuartalPremiumController::class, 'update'])->name('quartal-premium.update');
        Route::delete('delete/{id}', [Kpi\QuartalPremiumController::class, 'destroy']);
        Route::post('/set/status', [Kpi\QuartalPremiumStatusController::class, 'setActive']);
    });

    // Статистика для KPI
    Route::group(['prefix' => 'statistics', 'as' => 'kpi-statistic.'], function () {
        Route::get('kpi/user/{id}', [Kpi\KpiStatController::class, 'show'])->name('index');
        Route::get('kpi/users/', [Kpi\KpiStatController::class, 'fetchGroups'])->name('fetch');
        Route::any('kpi', [Kpi\KpiStatController::class, 'fetchKpis'])->name('fetchKpis');
        Route::any('kpi/read', [Kpi\KpiStatController::class, 'readKpis'])->name('readKpis');
        Route::any('kpi/groups-and-users', [Kpi\KpiStatController::class, 'fetchKpiGroupsAndUsers'])->name('fetchKpiGroupsAndUsers');
        Route::any('kpi/groups-and-users/{targetable_id}', [Kpi\KpiStatController::class, 'showKpiGroupAndUsers'])
            ->where('targetable_id', '[0-9]+');
        Route::get('kpi/annual-statistics', [Kpi\KpiStatController::class, 'getAnnualStatistics']);
        Route::any('bonuses', [Kpi\KpiStatController::class, 'fetchBonuses'])->name('fetchBonuses');
        Route::any('quartal-premiums', [Kpi\KpiStatController::class, 'fetchQuartalPremiums'])->name('fetchQuartalPremiums');
        Route::any('quartal-premiums/read', [Kpi\KpiStatController::class, 'readQuartalPremiums'])->name('quartal-premium.read');
        Route::any('workdays', [Kpi\KpiStatController::class, 'workdays']);
        Route::post('update-stat', [Kpi\KpiStatController::class, 'updateStat'])->name('updateStat');
        Route::get('activities', [Kpi\KpiStatController::class, 'getActivities'])->name('getActivites');
        Route::get('/kpi/user-groups', [Kpi\KpiStatController::class, 'groups']);
    });

    // Редактирование показателей
    Route::group(['prefix' => 'activities', 'as' => 'activities.', 'middleware' => 'superuser'], function () {
        Route::post('/get', [Kpi\IndicatorController::class, 'fetch'])->name('all');
        Route::get('/{id}', [Kpi\IndicatorController::class, 'get'])->name('one');
        Route::post('save', [Kpi\IndicatorController::class, 'save'])->name('save');
        Route::put('update', [Kpi\IndicatorController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [Kpi\IndicatorController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'kpi', 'as' => 'kpi.', 'middleware' => 'auth'], function () {
        Route::get('/', [Kpi\KpiController::class, 'index'])->name('index');
        Route::get('/bonus', [Kpi\KpiController::class, 'index'])->name('indexBonus');
        Route::get('/premium', [Kpi\KpiController::class, 'index'])->name('indexPremium');
        Route::get('/statistics', [Kpi\KpiController::class, 'index'])->name('indexStatistics');
        Route::get('/indicators', [Kpi\KpiController::class, 'index'])->name('indexIndicators');
        Route::post('/set/status', [Kpi\KpiStatusController::class, 'setActive']);
    });

    // Intellect Recruiting
    Route::get('/bpr/{hash}', [Services\IntellectController::class, 'contract']);
    Route::post('/bpr/{hash}', [Services\IntellectController::class, 'contract']);
    Route::get('/bpcontract', [Services\IntellectController::class, 'contract']);
    Route::any('/bp/job/agreement', [Services\IntellectController::class, 'contract']);
    Route::any('/bp/job/skype', [Services\IntellectController::class, 'skype']);
    Route::any('/bp/choose_time', [Services\IntellectController::class, 'choose_time']);
    Route::get('/maps', [Services\MapController::class, 'index'])->name('maps');
    Route::post('/selected-country/search/', [Services\MapController::class, 'selectedCountryAjaxSearch']);

    Route::get('/callibro/login', [Services\CallibroController::class, 'login']);

    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::any('/', [Services\NotificationControlller::class, 'get']);
        Route::any('/read', [Services\NotificationControlller::class, 'read']);
        Route::get('/unread-count', [Services\NotificationControlller::class, 'unReadCount']);
        Route::any('/set-read/', [Services\NotificationControlller::class, 'setRead']);
        Route::any('/set-read-all/', [Services\NotificationControlller::class, 'setAllRead']);
    });

    Route::prefix('news')->name('articles.')->middleware(['auth'])->group(function () {
        Route::get('/', [Article\NewsController::class, 'index'])->name('index');
        Route::post('/', [Article\ArticleController::class, 'store'])->name('store');
        Route::get('/get', [Article\ArticleController::class, 'index'])->name('get');
        Route::get('/count-unviewed', [Article\ArticleController::class, 'countUnviewed'])->name('countUnviewed');
        Route::get('{article_id}', [Article\ArticleController::class, 'show'])->name('show');
        Route::put('{article_id}', [Article\ArticleController::class, 'update'])->name('update');
        Route::delete('{article_id}', [Article\ArticleController::class, 'delete'])->name('delete');
        Route::post('{article_id}/vote', [Article\ArticleController::class, 'voteForArticle'])->name('vote');
        Route::post('/mark-articles-as-viewed', [Article\ArticleController::class, 'makeViewedArticles'])->name('make-article-viewed');


        Route::prefix('{article_id}')->name('actions.')->group(function () {
            Route::post('like', [Article\ArticleActionController::class, 'like'])->name('like');
            Route::post('favourite', [Article\ArticleActionController::class, 'favourite'])->name('favourite');
            Route::post('pin', [Article\ArticleActionController::class, 'pin'])->name('pin');
            Route::post('views', [Article\ArticleActionController::class, 'views'])->name('views');
        });

        Route::prefix('{article_id}/comments')->name('comments.')->group(function () {

            Route::get('/', [Article\Comments\ArticleCommentController::class, 'index'])->name('index');
            Route::post('/', [Article\Comments\ArticleCommentController::class, 'store'])->name('store');
            Route::delete('{comment_id}', [Article\Comments\ArticleCommentController::class, 'delete'])->name('delete');

            Route::prefix('{comment_id}')->name('actions.')->group(function () {
                Route::post('like', [Article\Comments\ArticleCommentActionController::class, 'like'])->name('like');
                Route::post('reaction', [Article\Comments\ArticleCommentActionController::class, 'reaction'])->name('reaction');
            });
        });
    });

    Route::prefix('dictionaries')->name('dictionaries.')->middleware(['auth'])->group(function () {
        Route::get('/', [Article\Dictionary\DictionaryController::class, 'index'])->name('index');
    });

    Route::prefix('birthdays')->name('birthdays.')->middleware(['auth'])->group(function () {
        Route::get('/', [Article\Birthday\BirthdayController::class, 'index'])->name('index');
        Route::post('{user}/send-gift', [Article\Birthday\BirthdayController::class, 'sendGift'])->name('send_gift');
    });

    Route::prefix('files')->name('files.')->middleware(['auth'])->group(function () {
        Route::post('/', [Article\Files\FileController::class, 'store'])->name('store');
        Route::delete('{file_id}', [Article\Files\FileController::class, 'delete'])->name('delete');
    });

    Route::prefix('uploads')->name('uploads.')->middleware(['auth:web',])->group(function () {
        Route::post('/', [Article\Uploads\UploadController::class, 'store'])->name('store');
    });


    Route::any('/upload/images/', [Learning\KnowBaseController::class, 'uploadimages']);
    Route::any('/upload/audio/', [Learning\KnowBaseController::class, 'uploadaudio']);

    Route::group([
        'prefix' => 'payment',
        'as' => 'payment.',
        'middleware' => 'auth'
    ], function () {
        Route::post('/', [Api\PaymentController::class, 'payment']);
        Route::post('/status', [Api\PaymentController::class, 'updateToTariffPayments']);
    });

    Route::group([
        'prefix' => 'tax',
        'as' => 'tax.'
    ], function () {
        Route::get('/', [Root\Tax\TaxController::class, 'get']);
        Route::get('/all', [Root\Tax\TaxController::class, 'all']);
        Route::post('/', [Root\Tax\TaxController::class, 'create']);
        Route::post('/set-assignee', [Root\Tax\TaxController::class, 'setAssigned']);
        Route::put('/', [Root\Tax\TaxController::class, 'update']);
        Route::delete('/{id}', [Root\Tax\TaxController::class, 'delete']);
    });

    Route::middleware(['check_tariff'])->group(function () {

        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::any('/activities', [User\ProfileController::class, 'activities']);
            Route::any('/courses', [User\ProfileController::class, 'courses']);
        });

        Route::group(['prefix' => 'kpi', 'as' => 'kpi.', 'middleware' => 'auth'], function () {
            Route::post('/get', [Kpi\KpiController::class, 'getKpis'])->name('get');
            Route::post('/save', [Kpi\KpiController::class, 'save'])->name('save');
            Route::put('/update', [Kpi\KpiController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [Kpi\KpiController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'company', 'as' => 'company.', 'middleware' => 'auth'], function () {
            Route::get('/get-owner', [Company\CompanyController::class, 'getCompanyOwner'])->name('get-owner');
        });
    });

    Route::group([
        'prefix' => 'mailing',
        'as' => 'mailing.'
    ], function () {
        Route::get('/', [Root\Mailing\MailingController::class, 'get']);
        Route::get('/find/{id}', [Root\Mailing\MailingController::class, 'find']);

        Route::post('/', [Root\Mailing\MailingController::class, 'create']);
        Route::put('/', [Root\Mailing\MailingController::class, 'update']);
        Route::delete('/{id}', [Root\Mailing\MailingController::class, 'delete']);
    });

    Route::group([
        'prefix' => 'notification-template',
        'as' => 'notification-template.'
    ], function () {
        Route::post('/apply-employee', [Root\Mailing\TriggerController::class, 'applyEmployee']);
        Route::post('/absent-internship', [Root\Mailing\TriggerController::class, 'absentInternship']);

        /**
         * API не используется так как перешли на крон. Но оставлю в списке
         */
        Route::post('/fired-employee/{id}', [Root\Mailing\TriggerController::class, 'firedEmployee']);
    });

    Route::prefix('')
        ->group(base_path('routes/referral.php'))
        ->middleware(['tenant']);
});

/**
 * API routes
 * instead of api.php
 */
Route::middleware(['api', 'tenant', 'not_admin_subdomain'])->group(function () {

    Route::group(['prefix' => 'api'], function () {

        Route::any('/intellect/start', [Services\IntellectController::class, 'start']); // Bitrix -> Admin -> Intellect
        Route::any('/intellect/save', [Services\IntellectController::class, 'save']);   // Intellect -> Admin -> Bitrix
        Route::any('/intellect/get_name', [Services\IntellectController::class, 'get_name']);   // Intellect -> Admin
        Route::any('/intellect/get_link', [Services\IntellectController::class, 'get_link']);   // Intellect -> Admin
        Route::any('/intellect/get_time', [Services\IntellectController::class, 'get_time']);   // Intellect -> Admin
        Route::any('/intellect/change_status', [Services\IntellectController::class, 'change_status']);   // Intellect -> Admin
        Route::any('/intellect/send_message', [Services\IntellectController::class, 'send_message']);   // Admin -> Intellect
        Route::any('/intellect/create_lead', [Services\IntellectController::class, 'create_lead']);   // Admin -> Intellect
        Route::any('/intellect/test', [Services\IntellectController::class, 'test']);   // Admin -> Intellect
        Route::any('/intellect/save_quiz_after_fire', [Services\IntellectController::class, 'quiz_after_fire']);   // Intellect -> Admin
        Route::any('/intellect/save_estimate_trainer', [Services\IntellectController::class, 'save_estimate_trainer']);

        Route::any('/headhunter/create_lead', [\App\Api\HeadHunter::class, 'createLead'])->name('create-lead');
        Route::any('/headhunter/check_lead', [\App\Api\HeadHunter::class, 'checkLead'])->name('check-lead');
        // Bitrix -> Admin
        Route::any('/bitrix/new-lead', [Services\IntellectController::class, 'newLead']);
        Route::any('/bitrix/edit-lead', [Services\IntellectController::class, 'editLead']);
        Route::any('/bitrix/edit-deal', [Services\IntellectController::class, 'editDeal']);
        Route::any('/bitrix/lose-deal', [Services\IntellectController::class, 'loseDeal']);
        Route::any('/bitrix/create-link', [Services\IntellectController::class, 'bitrixCreateLead']);
        Route::any('/bitrix/change-resp', [Services\IntellectController::class, 'changeResp']);
        Route::any('/bitrix/inhouse', [Services\IntellectController::class, 'inhouse']);


        Route::group(['prefix' => 'statistics'], function () {
            Route::any('/workdays', [Kpi\KpiStatController::class, 'workdays']);
        });

        Route::group(['prefix' => 'department', 'as' => 'department.'], function () {
            Route::get('/users/{id?}/{date?}', [Api\DepartmentUserController::class, 'getUsers'])->name('users');
            Route::get('/employees/{id?}/{date?}', [Api\DepartmentUserController::class, 'getEmployees'])->name('employees');
            Route::get('/trainees/{id?}/{date?}', [Api\DepartmentUserController::class, 'getTrainees'])->name('trainees');
            Route::get('/fired-users/{id?}/{date?}', [Api\DepartmentUserController::class, 'getFiredUsers'])->name('fired-users');
            Route::get('/fired-trainees/{id?}/{date?}', [Api\DepartmentUserController::class, 'getFiredTrainees'])->name('fired-trainees');
            Route::get('/check/user/{id}', [Api\DepartmentUserController::class, 'userInGroup']);
        });

        //Api Structure

        Route::group(['prefix' => 'structure', 'as' => 'structure.'], function () {
            Route::post('/store', [App\Http\Controllers\Api\Structure\StructureCardController::class, 'store'])->name('store');
            Route::get('/', [App\Http\Controllers\Api\Structure\StructureCardController::class, 'all'])->name('get-all');
            Route::put('/{structureCard}', [App\Http\Controllers\Api\Structure\StructureCardController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\Structure\StructureCardController::class, 'destroy'])->name('destroy');
        });

        Route::get('coordinates', [App\Http\Controllers\Coordinate\getCoordinateController::class, 'get'])->name('get-coordinate');

        Route::group(['prefix' => 'deals', 'as' => 'deals.'], function () {
            Route::any('/updated', [Deal\DealController::class, 'dealUpdatedWebhook']);
        });
    });
});


/**
 * Owners list
 * Admin.jobtron.org routes
 */
Route::middleware(['web', 'tenant', 'admin_subdomain'])->group(function () {

    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::get('/owners', [Admin\AdminController::class, 'owners']);
    });

    Route::group([
        'prefix' => 'managers',
        'as' => 'managers.'
    ], function () {
        Route::post('/put-owner', [Admin\Managers\ManagerOwnerController::class, 'putManagerToOwner']);
        Route::get('/owner', [Admin\Managers\ManagerOwnerController::class, 'getOwner']);
        Route::get('/get/{managerId?}', [Admin\Managers\ManagerController::class, 'get']);
    });

    Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
        Route::get('/', [Admin\AdminController::class, 'admins']);
        Route::post('/add', [Admin\AdminController::class, 'addAdmin']);
        Route::delete('/delete/{user}', [Admin\AdminController::class, 'deleteAdmin']);
        Route::post('/edit/{user}', [Admin\AdminController::class, 'edit']);
        Route::get('permissions/get', [Admin\AdminPermissionController::class, 'getPermissions']);
    });
    Route::get('roles/get', [Admin\AdminPermissionController::class, 'getRoles']);
});

Route::middleware(['web', 'tenant', 'not_admin_subdomain'])->group(function () {
    Route::group([
        'prefix' => 'managers',
        'as' => 'managers.'
    ], function () {
        Route::get('/owner-info', [Admin\Managers\ManagerPermissionController::class, 'getOwnerInfo']);
    });

    Route::group([
        'prefix' => 'owner',
        'as' => 'owner.'
    ], function () {
        Route::get('/manager', [Admin\Owners\OwnerController::class, 'getManager']);
        Route::get('/info', [Admin\OwnerController::class, 'info'])->name('info');
    });
});
