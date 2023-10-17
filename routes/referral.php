<?php


use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\Referral\StatisticsController;
use App\Http\Controllers\Referral\UserStatisticsController;

Route::group([
        'prefix' => 'referrals'
        , 'as' => 'referral.'
        , 'middleware' => [
            'only_bp',
            'auth.basic',
        ]
    ]
    , function () {
        Route::get('/url', [ReferralController::class, 'url']);
        Route::post('/request/{user}', [ReferralController::class, 'request']);
        Route::get('user/statistics', UserStatisticsController::class);
        Route::get('/statistics', StatisticsController::class);
    });