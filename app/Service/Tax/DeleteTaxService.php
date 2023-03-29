<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\Models\Tax;

class DeleteTaxService
{
    /**
     * @param int $id
     * @return bool
     */
    public function handle(int $id): bool
    {
        return Tax::getTaxById($id)->delete();
    }
}
