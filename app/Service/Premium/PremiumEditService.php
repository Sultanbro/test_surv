<?php
declare(strict_types=1);

namespace App\Service\Premium;

/**
* Класс для работы с Service.
*/
final class PremiumEditService
{
    public function edit(
        string $type,
        int $userId,
        string $amount,
        string $comment,
        string $date
    )
    {
        $pathToType = new ('App\\Service\\Premium\\Handlers\\' . ucfirst($type) . 'PremiumHandler');

        return $pathToType->handle(
            $userId, $amount, $comment, $date
        );
    }
}