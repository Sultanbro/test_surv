<?php

namespace App\Helpers;

use App\Repositories\CardRepository;
use App\Repositories\UserContactRepository;
use App\User;
use App\UserContact;
use Carbon\Carbon;

class UserHelper
{
    public static function showFiredEmployee(
        User $user,
        int $year,
        int $month
    )
    {
        if($user->deleted_at == '0000-00-00 00:00:00' || $user->deleted_at == null) { // Проверка не уволен ли сотрудник
            return true;
        } else {

            $dt1 = Carbon::parse($user->deleted_at); // День увольнения
            $dt2 = Carbon::create($year, $month, 30, 0, 0, 0); // Выбранный период

            if($dt1 >= $dt2) {
                if(count($user->fines) != 0) { // Проверка есть ли хоть одна fine user-a
                    return true;
                }
            } else if ($dt1->month == $dt2->month && $dt1->year == $dt2->year) { // Проверка совпадают ли месяцы
                return true;
            }
        }
    }

    /**
     * Кол-во дней.
     *
     * @param User $user
     * @return User
     */
    public static function workWithUs(
       User $user
    ): User
    {
        if($user->deleted_at != null && $user->deleted_at != '0000-00-00 00:00:00') {
            $user->worked_with_us = round((Carbon::parse($user->deleted_at)->timestamp - Carbon::parse($user->applied_at)->timestamp) / 3600 / 24) . ' дней';
        } else if(!$user->is_trainee && $user->deleted_at == null) {
            $user->worked_with_us = round((Carbon::now()->timestamp - Carbon::parse($user->created_at)->timestamp) / 3600 / 24) . ' дней';
        } else {
            $user->worked_with_us = 'Еще стажируется';
        }

        return $user;
    }

    /**
     * @param bool|int $is_trainee
     * @return array
     */
    public static function fireCause(
        ?bool $is_trainee = false
    ): array
    {
        if ($is_trainee)
        {
            $fireCauses = [
                'Был на основной работе',
                'Бросает трубку',
                'Вышел (-ла) из группы',
                'Забыл (-а), после обеда присутствует',
                'Нашел(-а) другую работу',
                'Не был на обучении / стажировке',
                'Не выходит на связь',
                'Не понравились условия оплаты труда',
                'Не сдал экзамен',
                'Не смог подключиться',
                'Не хочет долго стажироваться',
                'Не хочет работать 6 дней',
                'Отказ от стажировки',
                'Отсутствовал(а) более 3 дней',
                'По техническим причинам',
                'Пропал с обучения',
                'Ребенок заболел, не сможет совмещать',
                'Удалился (-ась), не актуально',
            ];
        } else {
            $fireCauses = [
                'Взял перерыв, позже возможно будет работать',
                'Дисциплинарные нарушения',
                'Дубликат, 2 учетки',
                'Заказчик снял с проекта',
                'Игнорирование предупреждений',
                'Не справился с обязанностями',
                'Конфликт с коллегами',
                'Нашел(-а) другую работу',
                'Неадекватная личность',
                'Некому за ребенком присматривать',
                'Не выходит на связь более 7 дней',
                'Не успевает по учебе',
                'Не устраивает график',
                'Не устраивает ЗП',
                'Не устраивает пункт в договоре',
                'Оказалось что есть вторая работа',
                'Переезд в другой город',
                'Плохие рабочие показатели/не справился',
                'По семейным обстоятельствам',
                'По состоянию здоровья',
                'По техническим причинам',
                'Проект закрыт. Снят с линии',
                'Решил(-а) работать оффлайн',
                'Слишком большая нагрузка',
            ];
        }

        return $fireCauses;
    }

    /**
     * @param int $userId
     * @param array $cards
     * @return void
     */
    public static function saveCards(int $userId, array $cards): void
    {
        foreach ($cards as $key => $card)
        {
            $cards[$key]['user_id'] = $userId;
        }

        (new CardRepository)->createMultipleCard($cards);
    }

    /**
     * @param int $userId
     * @param array $cards
     * @return void
     */
    public static function saveOrUpdateCards(int $userId, array $cards): void
    {
        foreach ($cards as $card)
        {
            $card['user_id'] = $userId;
            (new CardRepository)->createOrUpdateMultipleCard($card);
        }
    }

    /**
     * Сохранение доп телефонов для пользователя
     *
     * @param int $userId
     * @param array $phones
     * @return void
     */
    public static function saveContacts(
        int $userId,
        array $phones
    ): void
    {
        $contactRepo = new UserContactRepository();
        $contactsData = [];
        foreach ($phones as $phone)
        {
            $contactRepo->getAllUserContacts($userId)->delete();

            $contactsData[] = [
                'user_id' => $userId,
                'value' => $phone['value'],
                'name' => $phone['name'],
                'type'  => 'phone'
            ];
        }
        (new UserContactRepository)->createMultipleContact($contactsData);
    }
}