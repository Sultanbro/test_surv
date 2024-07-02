<?php

namespace App\Enums;

use App\Enums\Abstr\DictionaryEnum;
use App\Position;
use App\ProfileGroup;
use App\User;

class ArticleAvailableForTypeEnum extends DictionaryEnum
{
    const EMPLOYEE = 1;
    const PROFILE_GROUP = 2;
    const POSITION = 3;

    const TYPES = [
        self::EMPLOYEE => 'enum.article_available_for_type.employee',
        self::PROFILE_GROUP => 'enum.article_available_for_type.profile_group',
        self::POSITION => 'enum.article_available_for_type.position',
    ];

    const CLASSES = [
        self::EMPLOYEE => User::class,
        self::PROFILE_GROUP => ProfileGroup::class,
        self::POSITION => Position::class,
    ];

    protected static array $enumItems = self::TYPES;
}
