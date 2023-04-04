<?php

namespace App\Http\Requests\Notification;

use App\DTO\BaseDTO;
use App\DTO\Notification\SetReadDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class NotificationReadRequest extends BaseFormRequest
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
            'user_notification_id' => 'required|integer|exists:user_notifications,id'
        ];
    }

    /**
     * @return BaseDTO
     */
    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $userNotificationId = Arr::get($validated, 'user_notification_id');

        return new SetReadDTO($userNotificationId);
    }
}
