<?php

namespace App\Service\Award;
use App\Service\Award\AwardType\AccrualAwardService;
use App\Service\Award\AwardType\CertificateAwardService;
use App\Service\Award\AwardType\NominationAwardService;
use App\Service\Interfaces\Award\AwardBuilderInterface;
use Carbon\Exceptions\ParseErrorException;

class AwardBuilder implements AwardBuilderInterface
{

    /**
     * @param string $typeName
     * @return mixed
     * @throws \Exception
     */
    public function handle(string $typeName)
    {
        $method =  ucfirst($typeName) . 'Award';

        if (!method_exists($this, $method)) {
            throw new \Exception("Method $method not defined, please create");
        }

        return $this->{$method}();
    }

    /**
     * @return NominationAwardService
     */
    protected function nominationAward(): NominationAwardService
    {
        return new NominationAwardService();
    }

    /**
     * @return CertificateAwardService
     */
    protected function certificateAward(): CertificateAwardService
    {
        return new CertificateAwardService();
    }

    /**
     * @return AccrualAwardService
     */
    protected function accrualAward(): AccrualAwardService
    {
        return new AccrualAwardService();
    }

}