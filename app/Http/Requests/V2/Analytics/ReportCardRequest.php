<?php

namespace App\Http\Requests\V2\Analytics;

use App\DTO\Analytics\V2\ReportCardDto;
use Illuminate\Foundation\Http\FormRequest;

class ReportCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'group_id'      => 'required|integer|exists:profile_groups,id',
            'row_id'        => 'required|integer|exists:analytic_rows,id',
            'year'          => 'required|integer',
            'month'         => 'required|integer',
            'divide'        => 'required|numeric',
            'positions'     => 'required|array',
            'positions.*'   => 'required|integer|exists:position,id'
        ];
    }

    /**
     * @return ReportCardDto
     */
    public function toDto(): ReportCardDto
    {
        return ReportCardDto::fromArray($this->validated());
    }
}
