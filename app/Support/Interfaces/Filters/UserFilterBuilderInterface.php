<?php

namespace App\Support\Interfaces\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface UserFilterBuilderInterface
{
    public function all(array $request): object;

    public function deactivated(array $request): object;

    public function nonFilled(): object;

    public function trainees(array $request): object;

    public function active(array $request): object;
}