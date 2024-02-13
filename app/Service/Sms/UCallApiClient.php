<?php

namespace App\Service\Sms;

use App\Service\Tools\Debugger;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UCallApiClient implements ApiClientInterface
{
    protected string $baseUrl = 'https://cp.u-marketing.org';

    public function __construct(
        private readonly string $apiKey
    )
    {
    }

    public function request(
        string $method,
        string $url,
        array  $data
    ): array
    {
        if ($this instanceof ViaCurl) {
            return $this->buildRequestViaCurl($method, $url, $data);
        }

        return $this->buildRequest($method, $url, $data);
    }

    public function post($url, array $data): array
    {
        return $this->request('post', $url, $data);
    }

    private function buildRequest(string $method, string $url, array $data): ?array
    {
        $client = new Client(['verify' => false]);
        $request = Http::baseUrl($this->baseUrl);
        $request->setClient($client);
        try {
            return $request->{$method}($this->endpoint($url), $data)->json();
        } catch (Exception|RuntimeException $e) {
            Debugger::error($e);
            die($e);
        }
    }

    private function buildRequestViaCurl(string $method, string $url, array $data): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl . $this->endpoint($url));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, str($method)->lower() === 'post');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        $out = curl_exec($curl);
        Debugger::debug('error_in_ucall_api_service', curl_error($curl));
        curl_close($curl);
        return json_decode($out, true);
    }

    private function endpoint(string $url): string
    {
        return $url . '?apiKey=' . $this->apiKey;
    }
}