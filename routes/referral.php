<?php


use App\Http\Controllers\Referral\MarkAsPaidController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\Referral\StatisticsController;

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
        Route::post('/request/{user}', [ReferralController::class, 'request'])
            ->withoutMiddleware('auth.basic');
        Route::post('paid/{user}', [MarkAsPaidController::class, 'pay']);
        Route::get('/statistics', [StatisticsController::class, 'referrers']);
        Route::get('/statistics/user/{user?}', [StatisticsController::class, 'referrer']);
    });