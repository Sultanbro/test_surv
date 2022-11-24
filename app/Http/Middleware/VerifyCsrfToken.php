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
        '/bp/job/agreement/', 
        '/bp/job/skype/',  
        '/bp/choose_time', 
        '/statistics/kpi', 
        '/course-results/nullify',
        '/awards/store',
        '/awards/reward',
        '/award-types/store',
    ];

}
