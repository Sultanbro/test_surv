<?php

namespace App\Service\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UpdateDealInBitrix
{
    protected string $baseUrl = 'https://infinitys.bitrix24.kz/rest/66/';
    protected string $token = 'vkjaiufptgrokyk6';
    protected string $action = '/crm.automation.trigger/?target=DEAL_{{ID}}&code=n5vbh';

    public function __construct(
        private readonly Invoice $invoice
    )
    {

    }

    public function send(): void
    {
        $url = $this->buildUrl();
        $response = Http::post($url);
        $this->setDealId($response);
    }

    private function setDealId(Response $response): void
    {
        $leadId = $response->collect()->get('result');
        $this->invoice->lead_id = $leadId;
        $this->invoice->save();
    }

    private function buildUrl(): string
    {
        return $this->baseUrl . $this->token . str_replace('{{ID}}', $this->invoice->lead_id, $this->action);
    }
}