<?php

namespace App\Service\Interfaces\Award;

use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;

interface AwardInterface
{

    public function fetch(array $params): array;

    public function store(StoreAwardRequest $request);

    public function update(UpdateAwardRequest $request, Award $award);

}