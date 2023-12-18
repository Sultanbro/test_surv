<?php


use App\Http\Controllers\V2\Analytics\ActivityController;
use App\Http\Controllers\V2\Analytics\PositionController;
use App\Http\Controllers\V2\Analytics\TopController;
use App\Http\Controllers\V2\Analytics\V2AnalyticController;
use App\Http\Controllers\V2\Analytics\V2AnalyticInfoController;
use App\Http\Controllers\V2\Analytics\V2AnalyticGroupController;
use App\Http\Controllers\V2\Analytics\V2AnalyticUserInfoController;

Route::group([
    'middleware'    => ['analytics_permission'],
    'as'            => 'v2.analytics'
], function () {
    /**
     * Info about group and Fired users.
     */
    Route::get('/fired-info', [V2AnalyticUserInfoController::class, 'firedInfo'])->name('.fired-info');
    Route::get('/groups', [V2AnalyticGroupController::class, 'getGroups'])->name('.get-groups')
        ->middleware(['groups_activities_cached']);

    /**
     * Analytics pages.
     */
    Route::get('/analytics', [V2AnalyticInfoController::class, 'getAnalytics'])->name('.analytics')
        ->middleware(['analytics_cached', 'groups_activities_cached']);

    /**
     * Полезность и рентабельность.
     */
    Route::get('/performances', [V2AnalyticInfoController::class, 'getPerformances'])->name('.performances')
        ->middleware(['groups_activities_cached', 'analytics_cached']);

    /**
     * Декпомпозиция.
     */
    Route::get('/decompositions', [V2AnalyticInfoController::class, 'getDecompositions'])->name('.decompositions')
        ->middleware(['decomposition_cached']);

    /**
     * Показатели.
     */
    Route::get('/activities', [V2AnalyticInfoController::class, 'getActivities'])->name('.activities')
        ->middleware(['groups_activities_cached']);
});

Route::group([
    'as' => 'v2.analytics.action'
], function () {
    /**
     * Добавление новой строки.
     */
    Route::post('/add-row', [V2AnalyticController::class, 'addRow'])->name('.add.row');

    /**
     * Добавлить строку.
     */
    Route::post('/create', [V2AnalyticController::class, 'create'])->name('.create');

    /**
     * Получить маржу.
     */
    Route::get('/get-rentability', [TopController::class, 'getRentability'])->name('.rentability');

    Route::get('/get-utility', [TopController::class, 'getUtility'])->name('.utility');

    /**
     * Часы из табеля.
     */
    Route::post('/report-card', [V2AnalyticController::class, 'reportCard'])->name('.report-card');

    /**
     * Должности отдела.
     */
    Route::get('/positions', [PositionController::class, 'get'])->name('.positions');

    /**
     * Спидометры в марже.
     */
    Route::post('rentability/speedometers', [TopController::class, 'rentabilitySpeedometers'])->name('.speedometers');

    /**
     * Редактирование активности.
     */
    Route::post('activity/edit', [ActivityController::class, 'edit'])->name('.activity.edit');

    /**
     * Прогнозы.
     */
    Route::get('predicts/', [TopController::class, 'getPredicts'])->name('.predicts');
});