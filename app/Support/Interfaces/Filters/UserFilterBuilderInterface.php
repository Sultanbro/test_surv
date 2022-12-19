<?php

namespace App\Support\Interfaces\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface UserFilterBuilderInterface
{
    public function all(array $request);

    public function deactivated(array $request);

    public function nonFilled();

    public function trainees(array $request);

    public function active(array $request);
}