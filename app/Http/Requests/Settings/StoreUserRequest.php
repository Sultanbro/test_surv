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
            'description'       => ['nullable'],
            'position'          => ['required', 'numeric', 'exists:position,id'],
            'user_type'         => ['required', 'string', 'in:office,remote'],
            'birthday'          => ['string' , 'nullable'],
            'program_type'      => ['required', 'numeric'],
            'working_days'      => ['required', 'numeric'],
            'working_times'     => ['required', 'numeric'],
            'timezone'          => ['numeric'],
            'phone'             => ['nullable', 'string', 'min:10'],
            'phone_home'        => ['string', 'numeric', 'min:10'],
            'phone_husband'     => ['string', 'numeric', 'min:10'],
            'phone_relatives'   => ['string', 'numeric', 'min:10'],
            'phone_children'    => ['string', 'numeric', 'min:10'],
            'full_time'         => ['required', 'numeric'],
            'currency'          => ['string'],
            'weekdays'          => ['required', 'string'],
            'selectedCityInput' => ['nullable', 'string'],
            'working_city'      => ['nullable', 'numeric'],
            'file_name'         => ['file', 'mimes:jpg,png'],
            'head_group'        => ['numeric', 'exists:profile_groups,id'],
            'is_trainee'        => ['in:true,false'],
            'contacts'          => ['array'],
            'adaptation_talks'  => ['array'],
            'adaptation_talks.*.date'   => ['string'],
            'adaptation_talks.*.day'    => ['string'],
            'adaptation_talks.*.text'   => ['string'],
            'adaptation_talks.*.inter_id'  => ['string'],
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
            'jysan_cardholder'  => ['string'],
            'new_password'      => ['string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'tax'               => ['array'],
            'tax.*.amount'      => ['numeric'],
            'tax.*.percent'     => ['numeric'],
            'tax.*.user_id'     => ['numeric', 'exists:users,id'],
            'tax.*.name'        => ['string'],
            'taxes'             => ['array'],
            'taxes.*.amount'    => ['numeric'],
            'taxes.*.percent'   => ['numeric'],
            'taxes.*.user_id'   => ['numeric', 'exists:users,id'],
            'taxes.*.name'      => ['string'],
            'bitrix_id'         => ['numeric']
        ];
    }

    public function toDto(): StoreUserDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'id');
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
        $phoneHome = Arr::get($validated, 'phone_home');
        $phoneHusband = Arr::get($validated, 'phone_husband');
        $phoneRelatives = Arr::get($validated, 'phone_relatives');
        $phoneChildren = Arr::get($validated, 'phone_children');
        $fullTime = Arr::get($validated, 'full_time');
        $currency = Arr::get($validated, 'currency');
        $weekdays = Arr::get($validated, 'weekdays');
        $workingCountry = Arr::get($validated, 'selectedCityInput');
        $workingCity = Arr::get($validated, 'working_city');
        $fileName = Arr::get($validated, 'file_name');
        $headGroup = Arr::get($validated, 'head_group');
        $isTrainee = Arr::get($validated, 'is_trainee') == 'true';
        $contacts = Arr::get($validated, 'contacts');
        $adaptationTalks = Arr::get($validated, 'adaptation_talks');
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
        $newPassword = Arr::get($validated, 'new_password');
        $tax = Arr::get($validated, 'tax');
        $taxes = Arr::get($validated, 'taxes');
        $bitrixId = Arr::get($validated, 'bitrix_id');
        $timezone = Arr::get($validated, 'timezone');

        return new StoreUserDTO(
            $id,
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
            $phoneHome,
            $phoneHusband,
            $phoneRelatives,
            $phoneChildren,
            $fullTime,
            $currency,
            $weekdays,
            $workingCountry,
            $workingCity,
            $fileName,
            $headGroup,
            $isTrainee,
            $contacts,
            $adaptationTalks,
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
            $jysanCardholder,
            $newPassword,
            $tax,
            $taxes,
            $bitrixId,
            $timezone,
        );
    }
}
