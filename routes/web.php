<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/file/upload', 'UploadController@resumableUpload');

/* Самостоятельная отметка стажеров */
Route::get('/autocheck/{id}', 'Admin\TraineeController@autocheck'); // cтраница со ссылками для отметки стажерами
Route::post('/autocheck/{id}', 'Admin\TraineeController@save'); //
Route::get('/autochecker', 'Admin\TraineeController@autochecker');  // включить возможность отметки
Route::post('/autochecker/{id}', 'Admin\TraineeController@open');  // включить возможность отметки

Route::get('/login-as-employee/{id}', 'HomeController@loginAs');

Route::any('/auth', function () {
    return redirect('/');
});



Route::get('/learning/books', 'LearningController@books');
Route::get('/learning/videos', 'LearningController@videos');
Route::get('/courses', 'CourseController@index');

Route::get('/playlists/get/{id}', 'Video\VideoPlaylistController@get');
Route::post('/playlists/add-video', 'Video\VideoPlaylistController@add_video');
Route::post('/playlists/save-video', 'Video\VideoPlaylistController@save_video');
Route::post('/playlists/delete-video', 'Video\VideoPlaylistController@delete_video');
Route::post('/playlists/remove-video', 'Video\VideoPlaylistController@remove_video');
Route::post('/playlists/save', 'Video\VideoPlaylistController@save');
Route::post('/playlists/save-test', 'Video\VideoPlaylistController@saveTest');
Route::post('/playlists/video/update', 'Video\VideoController@updateVideo');


Route::get('/kb', 'KnowBaseController@index');
Route::get('/upbooks', 'UpbookController@index');
Route::get('/admin/upbooks', 'UpbookController@edit');
Route::get('/admin/upbooks/get', 'UpbookController@admin_get');
Route::post('/admin/upbooks/category/create', 'UpbookController@createCategory');
Route::post('/admin/upbooks/category/delete', 'UpbookController@deleteCategory');
Route::post('/admin/upbooks/tests/get', 'UpbookController@getTests');
Route::post('/admin/upbooks/save', 'UpbookController@save');
Route::post('/admin/upbooks/update', 'UpbookController@update');

Route::get('/admin/courses/get', 'CourseController@get');
Route::post('/admin/courses/delete', 'CourseController@delete');
Route::post('/admin/courses/save', 'CourseController@save');
Route::post('/admin/courses/create', 'CourseController@create');
Route::post('/admin/courses/get-item', 'CourseController@getItem');

Route::post('/course-results/get', 'CourseResultController@get');


Route::get('/kb/get', 'KnowBaseController@get');
Route::post('/kb/get', 'KnowBaseController@getPage');
Route::post('/kb/search', 'KnowBaseController@search');
Route::get('/kb/get-archived', 'KnowBaseController@getArchived');
Route::post('/kb/tree', 'KnowBaseController@getTree');

Route::post('/kb/page/update', 'KnowBaseController@updatePage');
Route::post('/kb/page/delete-section', 'KnowBaseController@deleteSection');
Route::post('/kb/page/restore-section', 'KnowBaseController@restoreSection');
Route::post('/kb/page/add-section', 'KnowBaseController@addSection');
Route::post('/kb/page/save-order', 'KnowBaseController@saveOrder');
Route::post('/kb/page/save-test', 'KnowBaseController@saveTest');
Route::post('/kb/page/create', 'KnowBaseController@createPage');
Route::post('/kb/page/delete', 'KnowBaseController@deletePage');
Route::post('/kb/page/update-section', 'KnowBaseController@updateSection');



Route::get('/test', 'TestController@test')->name('test');
Route::get('/wami', 'TestController@send_whatsapp');
Route::get('/', 'Admin\UserController@profile');




Route::get('/bonus', 'Admin\IndexController@bonus');
Route::any('/bonus/update/{id}', 'Admin\IndexController@bonusUpdate');

Route::get('/userroles', 'Admin\IndexController@userroles');
Route::any('/userroles/update/{id}', 'Admin\IndexController@userrolesUpdate');
Route::any('/max-session', 'Admin\IndexController@maxSession');



//Учет времени
Route::any('/timetracking', 'Admin\TimetrackingController@index');
Route::any('/timetracking/fines', 'Admin\TimetrackingController@fines');
Route::any('/timetracking/info', 'Admin\TimetrackingController@info');
Route::any('/timetracking/set-day', 'Admin\TimetrackingController@setDay');
Route::any('/timetracking/history', 'Admin\TimetrackingController@getHistory');

Route::post('/timetracking/kpi_save', 'Admin\KpiController@saveKPI');
Route::post('/timetracking/kpi_get', 'Admin\KpiController@getKPI');

Route::post('/timetracking/kpi_save_individual', 'Admin\KpiController@saveKpiIndividual');
Route::post('/timetracking/kpi_get_individual', 'Admin\KpiController@getKpiIndividual');

Route::get('/timetracking/books', 'Admin\BpartnersController@redirectToBpartnersBooks');


Route::get('/timetracking/kk', 'Admin\BpartnersController@corp_book');
Route::post('/corp_book/set-read/', 'Admin\UserController@corp_book_read');
Route::any('/timetrakicking/kk/ajax', 'Admin\BpartnersController@corp_book_ajax');
Route::get('/corp_book/{id}', 'LinkController@opened_corp_book');


Route::get('/quiz_after_fire', 'HomeController@quiz_after_fire'); 
Route::get('/estimate_trainer', 'HomeController@estimate_trainer'); 
Route::any('/efd', 'HomeController@estimate_first_day'); 
Route::any('/estimate_your_trainer', 'Admin\NpsController@estimate_your_trainer'); 


Route::any('/timetracking/user/{id}', 'Admin\UserController@profile');
Route::post('/timetracking/change-password', 'Admin\UserController@changePassword');
Route::any('/timetracking/settings', 'Admin\TimetrackingController@settings');
Route::any('/timetracking/settings/positions', 'Admin\TimetrackingController@positions');
Route::any('/timetracking/settings/positions/get', 'Admin\TimetrackingController@getPosition');
Route::any('/timetracking/settings/positions/save', 'Admin\TimetrackingController@savePositions');
Route::post('/timetracking/settings/positions/add', 'Admin\TimetrackingController@addPosition');
Route::post('/timetracking/settings/positions/delete', 'Admin\TimetrackingController@deletePosition');

Route::post('timetracking/settings/get_time_addresses', 'Admin\TimetrackingController@getTimeAddresses');
Route::post('timetracking/settings/save_time_addresses', 'Admin\TimetrackingController@saveTimeAddresses');


Route::any('/timetracking/settings/add', 'Admin\TimetrackingController@addsettings');
Route::any('/timetracking/settings/delete', 'Admin\TimetrackingController@deletesettings');
Route::any('/timetracking/user/save', 'Admin\TimetrackingController@saveprofile');
Route::post('/timetracking/settings/notifications/update', 'Admin\TimetrackingController@updateNotificationTemplate');
Route::get('/timetracking/settings/notifications/get', 'Admin\TimetrackingController@getNotificationTemplates');
Route::post('/timetracking/settings/notifications/user', 'Admin\TimetrackingController@getUserNotifications');
Route::post('/timetracking/settings/notifications/user/save', 'Admin\TimetrackingController@saveUserNotifications');

Route::post('/timetracking/settings/groups/importexcel', 'GroupsController@import');
Route::post('/timetracking/settings/groups/importexcel/save', 'GroupsController@saveTimes');

Route::post('/timetracking/analytics/activity/importexcel', 'Admin\ActivityController@import');
Route::post('/timetracking/analytics/activity/importexcel/save', 'Admin\ActivityController@saveTimes');

Route::post('/timetracking/analytics/decomposition/save', 'Admin\DecompositionController@save');
Route::delete('/timetracking/analytics/decomposition/delete', 'Admin\DecompositionController@delete');

Route::get('/timetracking/reports', 'Admin\TimetrackingController@reports');
Route::post('/timetracking/reports', 'Admin\TimetrackingController@getReports');
Route::post('/timetracking/reports/update/day', 'Admin\TimetrackingController@updateTimetrackingDay');

Route::any('/timetracking/starttracking', 'Admin\TimetrackingController@timetracking');
Route::any('/timetracking/status', 'Admin\TimetrackingController@trackerstatus');
Route::any('/timetracking/group/save', 'Admin\TimetrackingController@savegroup');
Route::any('/timetracking/group/delete', 'Admin\TimetrackingController@deletegroup');
Route::any('/timetracking/users', 'Admin\TimetrackingController@getusersgroup');
Route::any('/timetracking/users/group/save', 'Admin\TimetrackingController@saveusersgroup');
Route::any('/timetracking/users/bonus/save', 'GroupsController@saveBonuses');
Route::any('/timetracking/groups', 'Admin\TimetrackingController@getgroups');
Route::post('/timetracking/groups/restore', 'Admin\TimetrackingController@restoreGroup');
Route::any('/notifications/set-read/', 'Admin\UserController@setNotiRead');
Route::any('/notifications/set-read-all/', 'Admin\UserController@setNotiReadAll');

Route::any('/timetracking/reports/add-editors', 'Admin\TimetrackingController@usereditreports');
Route::any('/timetracking/reports/get-editors', 'Admin\TimetrackingController@modalcheckuserrole');
Route::any('/timetracking/reports/check-user', 'Admin\TimetrackingController@checkuserrole');

Route::any('/timetracking/reports/enter-report', 'Admin\TimetrackingController@enterreport');
Route::post('/timetracking/reports/enter-report/setmanual', 'Admin\TimetrackingController@enterreportManually');

Route::any('/timetracking/zarplata-table', 'Admin\TimetrackingController@zarplatatable');
Route::any('/timetracking/get-persons', 'Admin\UserController@getpersons');

// Video lessons and admin panel for uploading video
Route::any('/videolearning/{id?}', 'Video\VideolearningController@list')->name('videos.playlists');  
Route::any('/videolearning/playlists/{id}', 'Video\VideolearningController@playlist')->name('videos.playlist'); 
Route::resource('/videos', 'Video\VideoController'); 
Route::resource('/video_groups', 'Video\VideoGroupController'); 
Route::resource('/video_playlists', 'Video\VideoPlaylistController'); 
Route::resource('/video_categories', 'Video\VideoCategoryController'); 
Route::post('/videos/upload', 'Video\VideoController@upload')->name('videos.upload'); 
Route::post('/videos/add_comment', 'Video\VideoController@add_comment')->name('videos.add_comment'); 
Route::post('/videos/get_comment', 'Video\VideoController@get_comment')->name('videos.get_comment'); 
Route::post('/videos/upload_progress', 'Video\VideoController@upload_progress')->name('videos.upload_progress'); 
Route::post('/videos/views', 'Video\VideolearningController@views')->name('videos.views'); 

Route::post('/order-persons-to-group', 'Admin\TimetrackingController@orderPersonsToGroup'); // Заказ сотрудников в группы для Руководителей
Route::post('/timetracking/apply-person', 'Admin\TimetrackingController@applyPerson'); // Принятие на штат стажера


Route::get('/timetracking/create-person', 'Admin\UserController@createPerson')->name('users.create');
Route::post('/timetracking/person/store', 'Admin\UserController@storePerson')->name('users.store');
Route::get('/timetracking/edit-person', 'Admin\UserController@editperson')->name('users.edit'); 
Route::post('/timetracking/person/update', 'Admin\UserController@updatePerson')->name('users.update');

Route::post('/timetracking/edit-person/group', 'Admin\UserController@editPersonGroup'); // Удялть добавлять пользотвеля в группы
Route::post('/timetracking/edit-person/head_in_groups', 'Admin\UserController@setUserHeadInGroups'); // Удялть добавлять пользотвеля руководителем групп
Route::post('/timetracking/edit-person/book', 'Admin\UserController@editPersonBook'); // Удалять добавлять корп книги пользователю

Route::any('/timetracking/delete-person', 'Admin\UserController@deleteUser')->name('removeUser');
Route::any('/timetracking/recover-person', 'Admin\UserController@recoverUser')->name('recoverUser');

Route::any('/timetracking/an', 'Admin\AnalyticsController@index');
Route::any('/timetracking/analytics-page/getanalytics', 'Admin\AnalyticsController@get');

Route::any('/timetracking/analytics/save-call-base', 'Admin\GroupAnalyticsController@saveCallBase');

Route::any('/timetracking/analytics', 'Admin\GroupAnalyticsController@index');

Route::any('/timetracking/analytics/funnels', 'Admin\LeadController@funnel_segment');
Route::any('/timetracking/analytics/skypes/{id}', 'Admin\GroupAnalyticsController@redirectToBitrixDeal');
Route::any('/timetracking/getanalytics', 'Admin\GroupAnalyticsController@getanalytics');
Route::post('/timetracking/analytics/invite-users', 'Admin\GroupAnalyticsController@inviteUsers'); // Приглашение стажеров
Route::post('/timetracking/analytics/recruting/create-lead', 'Admin\GroupAnalyticsController@createRecrutingLead'); // Создание лидов вручную
Route::post('/timetracking/analytics/recruting/change-profile', 'Admin\GroupAnalyticsController@changeRecruiterProfile'); // Сменить профиль рекрутера

Route::get('/timetracking/nps', 'Admin\NpsController@index');
Route::post('/timetracking/nps', 'Admin\NpsController@fetch');

Route::get('/timetracking/top', 'Admin\TopController@index');
Route::post('/timetracking/top', 'Admin\TopController@fetch');
Route::post('/timetracking/top/save_top_value', 'Admin\TopController@saveTopValue');
Route::post('/timetracking/top/get-rentability', 'Admin\TopController@getRentability');
Route::post('/timetracking/top/create_gauge', 'Admin\TopController@createGauge');
Route::post('/timetracking/top/get_activities', 'Admin\TopController@getActivities');
Route::post('/timetracking/top/delete_gauge', 'Admin\TopController@deleteGauge');
Route::post('/timetracking/top/save_rent_max', 'Admin\TopController@saveRentMax');
Route::post('/timetracking/top/save_group_plan', 'Admin\TopController@saveGroupPlan');
Route::post('/timetracking/top/top_edited_value/update', 'Admin\TopController@updateTopEditedValue');
Route::post('/timetracking/top/proceeds/update', 'Admin\TopController@updateProceeds');

Route::any('/timetracking/get_kpi_totals', 'Admin\GroupAnalyticsController@get_kpi_totals');
Route::any('/timetracking/update-settings', 'Admin\GroupAnalyticsController@update');
Route::any('/timetracking/update-settings-extra', 'Admin\GroupAnalyticsController@updateExtra');
Route::post('/timetracking/get-totals-of-reports', 'Admin\TimetrackingController@getTotalsOfReports');

Route::any('/timetracking/quality-control', 'Admin\QualityController@index');
Route::any('/timetracking/quality-control/export', 'Admin\QualityController@exportExcel');
Route::any('/timetracking/quality-control/change-type', 'Admin\QualityController@changeType');
Route::any('/timetracking/quality-control/exportall', 'Admin\QualityController@exportAllExcel');
Route::post('/timetracking/quality-control/save', 'Admin\QualityController@saveRecord');
Route::post('/timetracking/quality-control/saveweekly', 'Admin\QualityController@saveWeeklyRecord');
Route::post('/timetracking/quality-control/delete', 'Admin\QualityController@deleteRecord'); 
Route::post('/timetracking/quality-control/records', 'Admin\QualityController@getRecords');
Route::post('/timetracking/quality-control/crits/save', 'Admin\QualityController@saveCrits');

Route::post('/timetracking/update-activity-total', 'Admin\GroupAnalyticsController@update_activity_total');
Route::any('/timetracking/update-settings-individually', 'Admin\GroupAnalyticsController@updateIndividually');
Route::get('/timetracking/analytics/activity/export', 'Admin\GroupAnalyticsController@exportActivityExcel');
Route::get('/timetracking/analytics/activity/exportxx', 'Admin\AnalyticsController@exportActivityExcel');

Route::get('/timetracking/salaries', 'Admin\SalaryController@index');
Route::get('/timetracking/salaries/export', 'Admin\SalaryController@exportExcel');

Route::post('/timetracking/salaries/get-total', 'Admin\SalaryController@getTotal');
Route::post('/timetracking/analytics/add-row', 'Admin\AnalyticsController@addRow');
Route::post('/timetracking/analytics/delete-row', 'Admin\AnalyticsController@deleteRow');
Route::post('/timetracking/analytics/dependency/remove', 'Admin\AnalyticsController@removeDependency');

Route::post('/timetracking/analytics/edit-stat', 'Admin\AnalyticsController@editStat');
Route::post('/timetracking/analytics/new-group', 'Admin\AnalyticsController@newGroup');
Route::post('/timetracking/analytics/create-activity', 'Admin\AnalyticsController@createActivity');
Route::post('/timetracking/analytics/edit-activity', 'Admin\AnalyticsController@editActivity');
Route::post('/timetracking/analytics/update-stat', 'Admin\AnalyticsController@updateUserStat');
Route::post('/timetracking/analytics/save-cell-activity', 'Admin\AnalyticsController@saveCellActivity');
Route::post('/timetracking/analytics/save-cell-time', 'Admin\AnalyticsController@saveCellTime');
Route::post('/timetracking/analytics/save-cell-sum', 'Admin\AnalyticsController@saveCellSum');
Route::post('/timetracking/analytics/save-cell-avg', 'Admin\AnalyticsController@saveCellAvg');
Route::post('/timetracking/analytics/change_order', 'Admin\AnalyticsController@change_order');
Route::post('/timetracking/analytics/delete_activity', 'Admin\AnalyticsController@delete_activity');
Route::post('/timetracking/analytics/add-depend', 'Admin\AnalyticsController@add_depend');
Route::post('/timetracking/analytics/archive_analytics', 'Admin\AnalyticsController@archive_analytics');
Route::post('/timetracking/analytics/restore_analytics', 'Admin\AnalyticsController@restore_analytics');
Route::post('/timetracking/analytics/add-formula-1-31', 'Admin\AnalyticsController@addFormula_1_31');
Route::post('/timetracking/analytics/add-remote-inhouse', 'Admin\AnalyticsController@addRemoteInhouse');


Route::post('/timetracking/salaries', 'Admin\SalaryController@salaries');
Route::post('/timetracking/salaries/update', 'Admin\SalaryController@update');
Route::post('/timetracking/salaries/recalc', 'Admin\SalaryController@recalc');
Route::post('/timetracking/salaries/edit-premium', 'Admin\SalaryController@editPremium');
Route::post('/timetracking/salaries/approve-salary', 'Admin\SalaryController@approveSalary');

Route::get('/timetracking/fine', 'Admin\FineController@index');
Route::put('/timetracking/fine', 'Admin\FineController@update');
Route::post('/timetracking/user-fine', 'Admin\UserFineController@update');

Route::get('/timetracking/exam', 'Admin\ExamController@index');
Route::post('/timetracking/exam', 'Admin\ExamController@getexams');
Route::post('/timetracking/exam/update', 'Admin\ExamController@update');

Route::post('/user/save/answer', 'Admin\ProfileController@saveAnswer');
Route::post('/position/save/desc', 'Admin\PositionController@savePositionDesc');




Route::get('/user/delete/{id}', 'Admin\IndexController@deleteUser');

Route::get('/books/{id?}', 'Admin\BpartnersController@books');
// Route::any('/books/update/{id?}', 'Admin\BpartnersController@booksupdate');
Route::any('/books/create/', 'Admin\BpartnersController@bookscreate');
Route::any('/books/delete/', 'Admin\BpartnersController@booksdelete');
Route::any('/books/order/', 'Admin\BpartnersController@orderbooks');
Route::any('/books/rename/', 'Admin\BpartnersController@renamebooks');
Route::any('/books/move/', 'Admin\BpartnersController@movebooks');
Route::post('/books/get_book/', 'Admin\BpartnersController@getBook');

Route::get('/bp_books', 'Admin\BookController@index');
Route::get('/bp_books/groups', 'Admin\BookController@groups');
Route::post('/bp_books/groups', 'Admin\BookController@group');
Route::post('/bp_books/groups/add', 'Admin\BookController@createBookGroup');
Route::post('/bp_books/groups/delete', 'Admin\BookController@deleteGroup');
Route::post('/bp_books/groups/add_books_to_group', 'Admin\BookController@addBooksToGroup');
Route::post('/bp_books/books', 'Admin\BookController@books');
Route::post('/bp_books/book/add', 'Admin\BookController@createBook');
Route::post('/bp_books/book/edit', 'Admin\BookController@editBook');
Route::post('/bp_books/book/delete', 'Admin\BookController@deleteBook');
Route::post('/bp_books/position_groups', 'Admin\BookController@positionGroups');
Route::post('/bp_books/position_groups/save', 'Admin\BookController@savePositionGroups');


Route::any('/pages/update/', 'Admin\BpartnersController@pagesupdate');
Route::any('/pages/create/', 'Admin\BpartnersController@pagescreate');
Route::any('/pages/delete/', 'Admin\BpartnersController@pagesdelete');
Route::any('/pages/order/', 'Admin\BpartnersController@orderpages');
Route::any('/pages/search/', 'Admin\BpartnersController@searchpages');
Route::any('/pages/rename/', 'Admin\BpartnersController@renamepages');
Route::any('/page/copy/', 'Admin\BpartnersController@copypages');
Route::any('/page/move/', 'Admin\BpartnersController@movepages');
Route::any('/upload/images/', 'Admin\BpartnersController@uploadimages');
Route::any('/upload/audio/', 'Admin\BpartnersController@uploadaudio');

Route::any('/books/password/', 'Admin\BpartnersController@password');

Route::any('/passwords', 'Admin\IndexController@passwords');


