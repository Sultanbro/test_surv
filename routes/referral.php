<?php


use App\Http\Controllers\Referral\MarkAsPaidController;
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
        Route::post('/request/{user}', [ReferralController::class, 'request'])
            ->withoutMiddleware('auth.basic');
        Route::get('statistics/user/{user?}', UserStatisticsController::class);
        Route::post('paid/{user}', MarkAsPaidController::class);
        Route::get('/statistics', [StatisticsController::class, 'list']);
    });