<?php

namespace App\Http\Requests\GroupUser;

use App\DTO\GroupUser\SaveUsersDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SaveUsersRequest extends FormRequest
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
            'group_id'  => ['required', 'numeric', 'exists:profile_groups,id'],
            'group_info'    => ['required', 'array'],
            'group_info.work_start'   => ['string', 'nullable'],
            'group_info.work_end'     => ['string', 'nullable'],
            'group_info.name'         => ['string'],
            'group_info.zoom_link'    => ['string', 'nullable'],
            'group_info.bp_link'      => ['string', 'nullable'],
            'group_info.workdays'     => ['numeric'],
            'group_info.payment_terms'    => ['nullable', 'min:3', 'max:10000'],
            'group_info.editable_time'    => ['boolean'],
            'group_info.paid_internship'  => ['boolean'],
            'group_info.quality'          => ['string', 'in:local,ucalls'],
            'group_info.show_payment_terms'  => ['boolean'],
            'dialer_id'     => ['nullable'],
            'script_id'     => ['nullable'],
            'talk_hours'    => ['nullable'],
            'talk_minutes'  => ['nullable']
        ];
    }

    /**
     * @return SaveUsersDTO
     */
    public function toDto(): SaveUsersDTO
    {
        $validated = $this->validated();

        $groupId = Arr::get($validated, 'group_id');
        $groupInfo = Arr::get($validated, 'group_info');
        $dialerId = Arr::get($validated, 'dialer_id');
        $scriptId = Arr::get($validated, 'script_id');
        $talkHours = Arr::get($validated, 'talk_hours');
        $talkMinutes = Arr::get($validated, 'talk_minutes');

        return new SaveUsersDTO(
            $groupId,
            $groupInfo,
            $dialerId,
            $scriptId,
            $talkHours,
            $talkMinutes
        );
    }
}
