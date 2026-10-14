<?php

namespace App\Enums;

class TimeTrackSettingEnum
{
    /**
     * Типы таб-ов.
     */
    const TABS = [
        1 => 'users',
        2 => 'positions',
        3 => 'groups',
        4 => 'fines',
        5 => 'notifications',
        6 => 'permissions',
        7 => 'checkLists'
    ];

    const ERROR_MESSAGE_FORBIDDEN = 'У вас нет доступа для страницы';
}