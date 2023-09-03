<?php


use App\Http\Controllers\V2\Analytics\V2AnalyticController;

Route::group([
    'middleware'    => 'analytics_permission',
    'as'            => 'v2.analytics'
], function () {
    /**
     * Info about group and Fired users.
     */
    Route::get('/fired-info', [V2AnalyticController::class, 'firedInfo'])->name('fired-info');
    Route::get('/groups', [V2AnalyticController::class, 'getGroups'])->name('get-groups');

    /**
     * Analytics pages.
     */
    Route::get('/analytics', [V2AnalyticController::class, 'getAnalytics'])->name('analytics');
    Route::get('/performances', [V2AnalyticController::class, 'getPerformances'])->name('performances');

});