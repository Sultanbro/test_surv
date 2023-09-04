<?php

namespace App\Traits;

use App\DTO\Analytics\V2\GetAnalyticDto;
use Illuminate\Http\Request;

trait AnalyticTrait
{
    /**
     * @param Request $request
     * @return GetAnalyticDto
     */
    public function requestToDto(
        Request $request
    ): GetAnalyticDto
    {
        $data = $request->all();

        return GetAnalyticDto::fromArray($data);
    }
}