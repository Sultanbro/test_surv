<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/sms/add',
        '/api/sms/check',
        '/api/call',
        '/api/call/status',
        '/sonic/api',
        '/callback/ivr',
        '/setting/payment/walletone',
        '/setting/reset',
        '/autocalls/income',
        '/setting/callback/kassa24',
        '/bitrix/app',
        '/bitrix/install',
        '/bitrix/handler',
        '/bitrix/bind',
        '/books/get/', 
        '/bp/job/agreement/', 
        '/bp/job/skype/', 
        '/bp/choose_time', 
        '/getpass',
        '/api/intellect/start',
        '/api/intellect/save',
        '/api/intellect/get_link',
        '/api/intellect/get_name',
        '/api/intellect/get_time',
        '/api/intellect/change_status',
        '/api/intellect/send_message',
        '/api/intellect/create_lead',
        '/api/intellect/save_quiz_after_fire',
        '/api/intellect/save_estimate_trainer',
        '/api/bitrix/new-lead',
        '/api/bitrix/edit-lead',
        '/api/bitrix/edit-deal',
        '/api/bitrix/lose-deal',
        '/api/bitrix/create-link',
        '/api/bitrix/change-resp',
        '/api/bitrix/inhouse',
        '/group-user/save',
        '/group-user/drop'
    ];


    // Route::any('/intellect/start', [IntellectController::class, 'start']); // Bitrix -> Admin -> Intellect
    // Route::any('/intellect/save', [IntellectController::class, 'save']);   // Intellect -> Admin -> Bitrix
    // Route::any('/intellect/get_name', [IntellectController::class, 'get_name']);   // Intellect -> Admin 
    // Route::any('/intellect/get_link', [IntellectController::class, 'get_link']);   // Intellect -> Admin 
    // Route::any('/intellect/get_time', [IntellectController::class, 'get_time']);   // Intellect -> Admin 
    // Route::any('/intellect/change_status', [IntellectController::class, 'change_status']);   // Intellect -> Admin 
    // Route::any('/intellect/send_message', [IntellectController::class, 'send_message']);   // Admin -> Intellect 
    // Route::any('/intellect/create_lead', [IntellectController::class, 'create_lead']);   // Admin -> Intellect 
    // Route::any('/intellect/test', [IntellectController::class, 'test']);   // Admin -> Intellect 
    // Route::any('/intellect/save_quiz_after_fire', [IntellectController::class, 'quiz_after_fire']);   // Intellect -> Admin 
    // Route::any('/intellect/save_estimate_trainer', [IntellectController::class, 'save_estimate_trainer']);  


    // Route::any('/bitrix/new-lead', [IntellectController::class, 'newLead']);   // Bitrix -> Admin 
    // Route::any('/bitrix/edit-lead', [IntellectController::class, 'editLead']);   // Bitrix -> Admin 
    // Route::any('/bitrix/edit-deal', [IntellectController::class, 'editDeal']);   // Bitrix -> Admin 
    // Route::any('/bitrix/lose-deal', [IntellectController::class, 'loseDeal']);   // Bitrix -> Admin 
    // Route::any('/bitrix/create-link',  [IntellectController::class, 'bitrixCreateLead']);   // Bitrix -> Admin 
    // Route::any('/bitrix/change-resp', [IntellectController::class, 'changeResp']);   // Bitrix -> Admin 
    // Route::any('/bitrix/inhouse',  [IntellectController::class, 'inhouse']);   // Bitrix -> Admin 
}
