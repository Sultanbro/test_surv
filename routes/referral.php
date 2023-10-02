<?php


use App\Http\Controllers\User\Referral\ReferralController;

Route::group([
        'prefix' => 'referrals'
        , 'as' => 'referral.'
    ]
    , function () {
        Route::get('/generate', [ReferralController::class, 'generate'])->name('referral');
        Route::post('/determinate/{referral}', [ReferralController::class, 'determinate'])->name('referer');
    });