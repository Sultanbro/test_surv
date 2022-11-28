<?php

namespace App\Service\Awards;
use App\Service\Awards\AwardTypes\AccrualAwardService;
use App\Service\Awards\AwardTypes\CertificateAwardService;
use App\Service\Awards\AwardTypes\NominationAwardService;
use App\Service\Interfaces\Award\AwardBuilderInterface;
use Carbon\Exceptions\ParseErrorException;

class AwardBuilder implements AwardBuilderInterface
{

    public function handle(string $typeName)
    {
        $method =  ucfirst($typeName) . 'Award';

        if (!method_exists($this, $method)) {
            throw new \Exception("Method $method not defined, please create");
        }

        return $this->{$method}();
    }

    protected function nominationAward(): NominationAwardService
    {
        return new NominationAwardService();
    }

    protected function certificateAward(): CertificateAwardService
    {
        return new CertificateAwardService();
    }

    protected function accrualAward(): AccrualAwardService
    {
        return new AccrualAwardService();
    }

}