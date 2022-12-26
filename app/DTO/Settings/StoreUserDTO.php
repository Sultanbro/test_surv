<?php

namespace App\DTO\Settings;

use Illuminate\Http\UploadedFile;

class StoreUserDTO
{
    /**
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string|null $description
     * @param int $positionId
     * @param string $userType
     * @param string $birthday
     * @param int $programType
     * @param int $workingDays
     * @param int|null $workTimes
     * @param string $phone
     * @param int $fullTime
     * @param string|null $workStartTime
     * @param string|null $workEndTime
     * @param string|null $currency
     * @param string|null $weekdays
     * @param string $workingCountry
     * @param string $workingCity
     * @param UploadedFile|null $fileName
     * @param int|null $headGroup
     * @param bool|null $isTrainee
     * @param array|null $contacts
     * @param array|null $cards
     * @param UploadedFile|null $file1
     * @param UploadedFile|null $file2
     * @param UploadedFile|null $file3
     * @param UploadedFile|null $file4
     * @param UploadedFile|null $file5
     * @param UploadedFile|null $file6
     * @param UploadedFile|null $file7
     * @param int|null $group
     * @param string|null $salary
     * @param string|null $cardNumber
     * @param string|null $kaspi
     * @param string|null $jysan
     * @param string|null $cardKaspi
     * @param string|null $cardJysan
     * @param string|null $kaspiCardholder
     * @param string|null $jysanCardholder
     */
    public function __construct(
        public string $name,
        public string $lastName,
        public string $email,
        public ?string $description,
        public int $positionId,
        public string $userType,
        public string $birthday,
        public int $programType,
        public int $workingDays,
        public ?int $workTimes,
        public string $phone,
        public int $fullTime,
        public ?string $workStartTime,
        public ?string $workEndTime,
        public ?string $currency,
        public ?string $weekdays,
        public string $workingCountry,
        public string $workingCity,
        public ?UploadedFile $fileName,
        public ?int $headGroup,
        public ?bool $isTrainee,
        public ?array $contacts,
        public ?array $cards,
        public ?UploadedFile $file1,
        public ?UploadedFile $file2,
        public ?UploadedFile $file3,
        public ?UploadedFile $file4,
        public ?UploadedFile $file5,
        public ?UploadedFile $file6,
        public ?UploadedFile $file7,
        public ?int $group,
        public ?string $salary,
        public ?string $cardNumber,
        public ?string $kaspi,
        public ?string $jysan,
        public ?string $cardKaspi,
        public ?string $cardJysan,
        public ?string $kaspiCardholder,
        public ?string $jysanCardholder
    )
    {}

    public function toArray(): array
    {
        return [
            'name'              => $this->name,
            'last_name'         => $this->lastName,
            'email'             => $this->email,
            'description'       => $this->description,
            'position_id'       => $this->positionId,
            'user_type'         => $this->userType,
            'birthday'          => $this->birthday,
            'program_type'      => $this->programType,
            'working_days'      => $this->workingDays,
            'working_times'     => $this->workTimes,
            'phone'             => $this->phone,
            'full_time'         => $this->fullTime,
            'work_start_time'   => $this->workStartTime,
            'work_end_time'     => $this->workEndTime,
            'currency'          => $this->currency,
            'weekdays'          => $this->weekdays,
            'working_country'   => $this->workingCountry,
            'working_city'      => $this->workingCity,
            'file_name'         => $this->fileName,
            'head_group'        => $this->headGroup,
            'is_trainee'        => $this->isTrainee,
            'contacts'          => $this->contacts,
            'cards'             => $this->cards,
            'file1'             => $this->file1,
            'file2'             => $this->file2,
            'file3'             => $this->file3,
            'file4'             => $this->file4,
            'file5'             => $this->file5,
            'file6'             => $this->file6,
            'file7'             => $this->file7,
            'group'             => $this->group,
            'salary'            => $this->salary,
            'card_number'       => $this->cardNumber,
            'kaspi'             => $this->kaspi,
            'jysan'             => $this->jysan,
            'card_kaspi'        => $this->cardKaspi,
            'card_jysan'        => $this->cardJysan,
            'kaspi_cardholder'  => $this->kaspiCardholder,
            'jysan_cardholder'  => $this->jysanCardholder
        ];
    }
}