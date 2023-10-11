<?php


use App\Http\Controllers\Referral\ReferralController;

Route::group([
          'prefix' => 'referrals'
        , 'as' => 'referral.'
        , 'middleware' => ['only_bp']
    ]
    , function () {
        Route::get('/generate', [ReferralController::class, 'generate'])->name('referral');
        Route::post('/determinate/{referral}', [ReferralController::class, 'request'])->name('referer');
    });