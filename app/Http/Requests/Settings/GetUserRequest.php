<?php

namespace App\Http\Requests\Settings;

use App\DTO\Settings\GetUsersDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class GetUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'filter'        => ['required', 'string'],
            'job'           => ['required', 'numeric'],
            'segment'       => ['required', 'numeric'],
            'start_date'    => ['string'],
            'end_date'      => ['string'],
            'start_date_deactivate' => ['string', 'date_format:Y-m-d'],
            'end_date_deactivate'   => ['string', 'date_format:Y-m-d'],
            'start_date_applied'    => ['string', 'date_format:Y-m-d'],
            'end_date_applied'      => ['string', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return GetUsersDTO
     */
    public function toDto(): GetUsersDTO
    {
        $validated = $this->validated();

        $type       = Arr::get($validated, 'filter');
        $positionId = Arr::get($validated, 'job');
        $segment    = Arr::get($validated, 'segment');
        $startDate  = Arr::get($validated, 'start_date');
        $endDate    = Arr::get($validated, 'end_date');
        $startDateDeactivate = Arr::get($validated, 'start_date_deactivate');
        $endDateDeactivate  = Arr::get($validated, 'end_date_deactivate');
        $startDateApplied   = Arr::get($validated, 'start_date_applied');
        $endDateApplied     = Arr::get($validated, 'end_date_applied');

        return new GetUsersDTO(
            $type,
            $positionId,
            $segment,
            $startDate,
            $endDate,
            $startDateDeactivate,
            $endDateDeactivate,
            $startDateApplied,
            $endDateApplied
        );
    }
}
