<?php

declare(strict_types=1);

namespace App\Service\Settings;

use App\AdaptationTalk;
use App\DayType;
use App\DTO\Settings\UpdateUserDTO;
use App\Enums\ErrorCode;
use App\Events\UserUpdatedEvent;
use App\Facade\Referring;
use App\Helpers\FileHelper;
use App\Helpers\UserHelper;
use App\Models\CentralUser;
use App\Models\Coordinate;
use App\Models\UserCoordinate;
use App\Repositories\UserRepository;
use App\Service\TaxService;
use App\User;
use Carbon\Carbon;
use Exception;
use Hash;
use Throwable;

final class UserUpdateService
{
    public function __construct(
        public UserRepository $userRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function updateUser(
        UpdateUserDTO $userDTO
    ): User
    {
        $user = $this->userRepository->userWithRelations($userDTO->userId, [
            'zarplata',
            'photo',
            'downloads',
            'user_description'
        ]);
        $this->emailCheck($user, $userDTO->email);
        $user = $this->updateUserData($user, $userDTO);

        if ($userDTO->adaptationTalks) {
            $this->setAdaptationTalks($user->id, $userDTO->adaptationTalks);
        }

        $this->changeTraineeToEmployee($user, $userDTO->isTrainee);

        $this->setBitrix($user, $userDTO->bitrixId);

        if ($userDTO->cards) {
            UserHelper::saveOrUpdateCards($user->id, $userDTO->cards);
        }

        if ($userDTO->contacts) {
            UserHelper::saveContacts($user->id, $userDTO->contacts['phone']);
        }

        if (
            $userDTO->file1 || $userDTO->file2 ||
            $userDTO->file3 || $userDTO->file4 ||
            $userDTO->file5 || $userDTO->file6 ||
            $userDTO->file7
        ) {
            FileHelper::storeDocumentsFile([
                'dog_okaz_usl' => $userDTO->file1,
                'sohr_kom_tainy' => $userDTO->file2,
                'dog_o_nekonk' => $userDTO->file3,
                'trud_dog' => $userDTO->file4,
                'ud_lich' => $userDTO->file5,
                'photo' => $userDTO->file6,
                'archive' => $userDTO->file7
            ], $user->id);
        }

        $this->userRepository->updateOrCreateSalary(
            $user->id,
            $userDTO->cardNumber,
            $userDTO->kaspi,
            $userDTO->jysan,
            $userDTO->cardKaspi,
            $userDTO->cardJysan,
            $userDTO->kaspiCardholder,
            $userDTO->jysanCardholder,
            $userDTO->salary,
        );

        return $user;
    }

    /**
     * @param User $user
     * @param string $email
     * @return void
     */
    private function emailCheck(
        User   $user,
        string $email,

    ): void
    {
        $existEmail = $this->userRepository->getUserByEmail($email);
        if ($existEmail && $email != $user->email) {
            if ($existEmail->deleted_at != null) {
                $text = '<p>Нужно ввести другую почту, так как сотрудник c таким email ранее был уволен:</p>';
                $text .= '<table class="table" style="border-collapse: separate; margin-bottom: 15px;">';
                $text .= '<tr><td><b>Имя:</b></td><td>' . $existEmail->name . '</td></tr>';
                $text .= '<tr><td><b>Фамилия:</b></td><td>' . $existEmail->last_name . '</td></tr>';
                $text .= '<tr><td><b>Email:</b></td><td><a href="/timetracking/edit-person?id=' . $existEmail->id . '" target="_blank"> ' . $existEmail->email . '</a></td></tr>';
                $text .= '<tr><td><b>Дата увольнения:</b></td><td>' . Carbon::parse($existEmail->deleted_at)->setTimezone('Asia/Dacca') . '</td></tr>';
                $text .= '</table>';
                redirect()->to('/timetracking/edit-person?id=' . $user->id)->withInput()->withErrors($text);
                return;
            }

            $text = 'Нужно ввести другую почту, так как сотрудник c таким email уже существует! <br>' . $email . '<br><a href="/timetracking/edit-person?id=' . $existEmail->id . '"   target="_blank">' . $existEmail->last_name . ' ' . $existEmail->name . '</a>';
            redirect()->to('/timetracking/edit-person?id=' . $user->id)->withInput()->withErrors($text);
        }
    }

    /**
     * @param User $user
     * @param UpdateUserDTO $userDTO
     * @return User
     */
    private function updateUserData(
        User          $user,
        UpdateUserDTO $userDTO
    ): User
    {
        $params = [
            'email' => $userDTO->email,
            'name' => $userDTO->name,
            'last_name' => $userDTO->lastName,
            'phone' => $userDTO->phone,
            'phone_1' => $userDTO->phoneHome,
            'phone_2' => $userDTO->phoneHusband,
            'phone_3' => $userDTO->phoneRelatives,
            'phone_4' => $userDTO->phoneChildren,
            'birthday' => $userDTO->birthday,
            'full_time' => $userDTO->fullTime,
            'description' => $userDTO->description,
            'currency' => $userDTO->currency ?? 'kzt',
            'position_id' => $userDTO->positionId ?? 0,
            'user_type' => $userDTO->userType,
            'timezone' => $userDTO->timezone,
            'program_id' => $userDTO->programType,
            'working_day_id' => $userDTO->workingDays,
            'working_time_id' => $userDTO->workTimes,
            'work_chart_id' => $userDTO->workChartId,
            'weekdays' => $userDTO->weekdays,
            'first_work_day' => $userDTO->firstWorkDay,
            'working_country' => $userDTO->workingCountry,
            'working_city' => $userDTO->workingCity,
            'uin' => $userDTO->uin,
        ];

        if (!empty($userDTO->newPassword)) {
            $params['password'] = Hash::make($userDTO->newPassword);
        }

        if (!empty($userDTO->coordinates)) {
            $params['coordinate_id'] = $this->setCoordinate($userDTO->coordinates);
        }

        User::query()->where('id', $user->id)->update($params);

        /** @var CentralUser $centralUser */
        $centralUser = CentralUser::query()->where('email', $user->email)->first();

        if ($centralUser) {
            if ($userDTO->newPassword) {
                $centralUser->password = Hash::make($userDTO->newPassword);
            }
            $centralUser->email = $userDTO->email;
            $centralUser->name = $userDTO->name;
            $centralUser->last_name = $userDTO->lastName;
            $centralUser->phone = $userDTO->phone;
            $centralUser->birthday = $userDTO->birthday;
            $centralUser->currency = $userDTO->currency ?? 'kzt';
            $centralUser->country = $userDTO->workingCountry;
            $centralUser->save();
        }

        event(new UserUpdatedEvent($user->id));
        return $user;
    }

    private function setCoordinate(array $coordinatesArray)
    {
        $coordinate = UserCoordinate::query()
            ->where('geo_lat', $coordinatesArray['geo_lat'])
            ->where('geo_lon', $coordinatesArray['geo_lon'])
            ->first();

        if ($coordinate) {
            return $coordinate->id;
        } else {
            $coordinate = UserCoordinate::query()->create([
                'geo_lat' => $coordinatesArray['geo_lat'],
                'geo_lon' => $coordinatesArray['geo_lon']
            ]);
            return $coordinate->id;
        }
    }

    /**
     * @param int $userId
     * @param array $talks
     * @return void
     */
    private function setAdaptationTalks(
        int   $userId,
        array $talks
    ): void
    {
        try {
            foreach ($talks as $talk) {
                $at = AdaptationTalk::query()->where('user_id', $userId)->where('day', $talk['day'])->first();
                if ($at) {
                    $at->inter_id = $talk['inter_id'];
                    $at->text = $talk['text'];
                    $at->date = $talk['date'];
                    $at->save();
                } else {
                    AdaptationTalk::query()->create([
                        'inter_id' => $talk['inter_id'],
                        'text' => $talk['text'],
                        'day' => $talk['day'],
                        'date' => $talk['date'],
                        'user_id' => $userId,
                    ]);
                }
            }
        } catch (Throwable $exception) {
            redirect()->withErrors(ErrorCode::MESSAGES['save']);
        }
    }

    /**
     * @param User $user
     * @param $isTrainee
     * @return void
     */
    private function changeTraineeToEmployee(
        User $user,
             $isTrainee
    ): void
    {
        if ($isTrainee == "false") {
            $user->description()->update([
                'is_trainee' => 0
            ]);

            $user->daytypes()
                ->where('date', now()->format('Y-m-d'))
                ->update([
                    'type' => DayType::DAY_TYPES['APPLIED']
                ]);

            Referring::syncReferrerStatus($user);
            Referring::salaryForCertificate($user);
        }
    }

    /**
     * @param User $user
     * @param int|null $bitrixId
     * @return void
     */
    private function setBitrix(
        User $user,
        ?int $bitrixId
    ): void
    {
        if (isset($bitrixId)) {
            $user->user_description()->update([
                'bitrix_id' => $bitrixId
            ]);
        }
    }

    /**
     * @throws Exception
     */
    private function setTaxes(
        array $data
    ): void
    {
        try {
            (new TaxService)->userTax($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param User $user
     * @param string|null $country
     * @return User
     */
    private function setCountryAndCity(
        User    $user,
        ?string $country
    )
    {
        $user->working_country = $country;
        $user->working_city = Coordinate::query()->where('country', 'LIKE', "%$country%")->first()->id ?? null;

        return $user;
    }
}
