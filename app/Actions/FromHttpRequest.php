<?php

namespace App\Actions;

use Illuminate\Foundation\Http\FormRequest;

interface FromHttpRequest
{
    public static function fromRequest(FormRequest $request): mixed;
}