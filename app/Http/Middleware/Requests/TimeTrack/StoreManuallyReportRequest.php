<?php

namespace App\Http\Requests\TimeTrack;

use App\DTO\TimeTrack\StoreManuallyReportDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreManuallyReportRequest extends FormRequest
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
            'year'  => ['required', 'numeric'],
            'month' => ['required', 'numeric'],
            'day'   => ['required', 'numeric'],
            'time'   => ['required', 'string'],
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'comment' => ['string', 'nullable']
        ];
    }

    /**
     * @return StoreManuallyReportDTO
     */
    public function toDto(): StoreManuallyReportDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');
        $year   = Arr::get($validated, 'year');
        $month  = Arr::get($validated, 'month');
        $day    = Arr::get($validated, 'day');
        $time    = Arr::get($validated, 'time');
        $comment = Arr::get($validated, 'comment');

        return  new StoreManuallyReportDTO(
            $userId,
            $year,
            $month,
            $day,
            $time,
            $comment
        );
    }
}
