<?php

namespace App\Http\Controllers\Mailing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mailing\CreateMailingRequest;
use App\Service\Mailing\CreateMailingService;
use Illuminate\Http\Request;

class MailingController extends Controller
{
    public function create(CreateMailingRequest $request, CreateMailingService $service)
    {

    }
}
