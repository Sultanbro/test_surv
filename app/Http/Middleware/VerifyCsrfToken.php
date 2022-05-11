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
        '/getpass'
    ];
}
