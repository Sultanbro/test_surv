<?php

namespace App\Service;

use App\Classes\Helpers\Phone;
use App\Position;
use App\Response\JsonApiResponse;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixIntegrationService
{
    public Http $client;
    public string  $host;
    public string $token;

    public function __construct(
        Http $client
    ){
        $this->client = $client;
        $this->host   = config('bitrix')['host'];
        $this->token  = config('bitrix')['token'];
    }

    /**
     * Получаем все задачи касаемо авторизованного пользователя
     * @return array
     */
    public function getTasks(): array
    {
        try {
            $bitrixUser  = $this->checkCurrentUserBitrix();

            $link        = $this->host . $this->token . '/tasks.task.list';
            $apiResponse = $this->client::get($link, [
                'filter' => [
                    'RESPONSIBLE_ID' => (int) $bitrixUser['ID'],
                    'CLOSED_DATE'    => ''
                ]
            ]);
            $response = new JsonApiResponse($apiResponse);

            return $response->getData();

        }catch (\Exception $exception) {
            Log::error(get_class() . ' обноружилась ошибка с сообщением ' . $exception);

            throw new \DomainException(get_class() . ' обноружилась ошибка с сообщением ' . $exception);
        }
    }

    /**
     * Получем текущего пользователя в Bitrix24
     * @return array
     */
    private function checkCurrentUserBitrix(): array
    {
        try {
            $this->checkPositionOfUser();

            $user = Auth::user();

            $link = $this->host . $this->token . 'user.get';

            $bitrixUserResponse = $this->client::get($link, [
                'email' => $user->email
            ]);

            $response = new JsonApiResponse($bitrixUserResponse->json());

            return collect($response->getData())->first();

        }catch (\DomainException $exception)
        {
            Log::error($exception);

            throw new \DomainException(get_class(). ' [Есть ошибка при обработке данных: ]' . $exception);
        }
    }

    /**
     * Проверяем позицию текущего пользователя.
     * Важно!
     * @return void
     */
    private function checkPositionOfUser(): void
    {
        $position = Auth::user()->positions()->first();

        if (in_array($position->id, [Position::OPERATOR_ID, Position::INTERN_ID]))
        {
            Log::error('Позиция пользователя должен быть выше Оператор, Стажер');

            throw new \DomainException('Позиция пользователя должен быть выше Оператор, Стажер' );
        }
    }
}