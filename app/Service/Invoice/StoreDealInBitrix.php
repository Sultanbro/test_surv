<?php

namespace App\Service\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class StoreDealInBitrix
{
    protected string $baseUrl = 'https://infinitys.bitrix24.kz/rest/66/';
    protected string $token = 'ke750bp42k74ytn2';
    protected string $action = '/crm.deal.add.json';

    public function __construct(
        private readonly Invoice $invoice
    )
    {

    }

    public function send(): void
    {
        $url = $this->buildUrl();
        $body = $this->buildBody();
        $response = Http::post($url, $body);
        $this->setDealId($response);
    }

    private function buildBody(): array
    {
        return [
            "fields" => [
                "TITLE" => "Jobtron.org - Платеж практикум: {$this->invoice->id}",
                "TYPE_ID" => "SALE",
                "STAGE_ID" => "C38:NEW",
                "OPPORTUNITY" => 12500,
                "CURRENCY_ID" => "kzt",
                "RESPONSIBLE_ID" => 1,
                "CATEGORY_ID" => "38"
            ]
        ];
    }

    private function setDealId(Response $response): void
    {
        $leadId = $response->collect()->get('result');
        $this->invoice->lead_id = $leadId;
        $this->invoice->save();
    }

    private function buildUrl(): string
    {
        return $this->baseUrl . $this->token . $this->action;
    }
}