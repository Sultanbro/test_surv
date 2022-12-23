<?php

namespace App\Http\Requests\Settings;

use App\DTO\Settings\StoreUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreUserRequest extends FormRequest
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
            'name'              => ['required', 'string', 'min:3', 'max:60'],
            'last_name'         => ['required', 'string', 'min:3', 'max:60'],
            'email'             => ['required', 'string', 'email'],
            'description'       => ['string', 'min:3', 'max:255'],
            'position'          => ['required', 'numeric', 'exists:position,id'],
            'user_type'         => ['required', 'string', 'in:office,remote'],
            'birthday'          => ['required', 'string'],
            'program_type'      => ['required', 'numeric'],
            'working_days'      => ['required', 'numeric'],
            'working_times'     => ['required', 'numeric'],
            'phone'             => ['required', 'string'],
            'full_time'         => ['required', 'numeric'],
            'work_start_time'   => ['string'],
            'work_end_time'     => ['string'],
            'currency'          => ['string'],
            'weekdays'          => ['required', 'string'],
            'selectedCityInput' => ['required', 'string'],
            'working_city'      => ['required', 'string'],
            'file_name'         => ['file', 'mimes:jpg,png'],
            'head_group'        => ['numeric', 'exists:profile_groups,id'],
            'is_trainee'        => ['boolean'],
            'contacts'          => ['array'],
            'contacts.phone'    => ['required_with:contacts', 'array'],
            'contacts.phone.*.name'  => ['string'],
            'contacts.phone.*.value' => ['string'],
            'cards'             => ['array'],
            'cards.bank'        => ['required_with,cards', 'string'],
            'cards.country'     => ['required_with,cards', 'string'],
            'cards.cardholder'  => ['required_with,cards', 'string'],
            'cards.phone'       => ['required_with,cards', 'string'],
            'cards.number'      => ['required_with,cards', 'string'],
            'file1'             => ['file'],
            'file2'             => ['file'],
            'file3'             => ['file'],
            'file4'             => ['file'],
            'file5'             => ['file'],
            'file6'             => ['file'],
            'file7'             => ['file'],
            'group'             => ['required', 'numeric', 'exists:profile_groups,id'],
            'zarplata'          => ['numeric'],
            'card_number'       => ['string'],
            'kaspi'             => ['string'],
            'jysan'             => ['string'],
            'card_kaspi'        => ['string'],
            'card_jysan'        => ['string'],
            'kaspi_cardholder'  => ['string'],
            'jysan_cardholder'  => ['string']
        ];
    }

    public function toDto(): StoreUserDTO
    {
        $validated = $this->validated();

        $name = Arr::get($validated, 'name');
        $lastName = Arr::get($validated, 'last_name');
        $email = Arr::get($validated, 'email');
        $description = Arr::get($validated, 'description');
        $positionId = Arr::get($validated, 'position');
        $userType = Arr::get($validated, 'user_type');
        $birthday = Arr::get($validated, 'birthday');
        $programType = Arr::get($validated, 'program_type');
        $workingDays = Arr::get($validated, 'working_days');
        $workTimes = Arr::get($validated, 'working_times');
        $phone = Arr::get($validated, 'phone');
        $fullTime = Arr::get($validated, 'full_time');
        $workStartTime = Arr::get($validated, 'work_start_time');
        $workEndTime = Arr::get($validated, 'work_end_time');
        $currency = Arr::get($validated, 'currency');
        $weekdays = Arr::get($validated, 'weekdays');
        $workingCountry = Arr::get($validated, 'selectedCityInput');
        $workingCity = Arr::get($validated, 'working_city');
        $fileName = Arr::get($validated, 'file_name');
        $headGroup = Arr::get($validated, 'head_group');
        $isTrainee = Arr::get($validated, 'is_trainee');
        $contacts = Arr::get($validated, 'contacts');
        $cards = Arr::get($validated, 'cards');
        $file1 = Arr::get($validated, 'file1');
        $file2 = Arr::get($validated, 'file2');
        $file3 = Arr::get($validated, 'file3');
        $file4 = Arr::get($validated, 'file4');
        $file5 = Arr::get($validated, 'file5');
        $file6 = Arr::get($validated, 'file6');
        $file7 = Arr::get($validated, 'file7');
        $group = Arr::get($validated, 'group');
        $salary = Arr::get($validated, 'zarplata');
        $cardNumber = Arr::get($validated, 'card_number');
        $kaspi = Arr::get($validated, 'kaspi');
        $jysan = Arr::get($validated, 'jysan');
        $cardKaspi = Arr::get($validated, 'card_kaspi');
        $cardJysan = Arr::get($validated, 'card_jysan');
        $kaspiCardholder = Arr::get($validated, 'kaspi_cardholder');
        $jysanCardholder = Arr::get($validated, 'jysan_cardholder');

        return new StoreUserDTO(
            $name,
            $lastName,
            $email,
            $description,
            $positionId,
            $userType,
            $birthday,
            $programType,
            $workingDays,
            $workTimes,
            $phone,
            $fullTime,
            $workStartTime,
            $workEndTime,
            $currency,
            $weekdays,
            $workingCountry,
            $workingCity,
            $fileName,
            $headGroup,
            $isTrainee,
            $contacts,
            $cards,
            $file1,
            $file2,
            $file3,
            $file4,
            $file5,
            $file6,
            $file7,
            $group,
            $salary,
            $cardNumber,
            $kaspi,
            $jysan,
            $cardKaspi,
            $cardJysan,
            $kaspiCardholder,
            $jysanCardholder
        );
    }
}
