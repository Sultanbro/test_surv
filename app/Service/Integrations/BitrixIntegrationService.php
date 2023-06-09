<?php

namespace App\Service\Integrations;

use App\Position;
use App\Support\Response\JsonApiResponse;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BitrixIntegrationService
{
    public string  $host;
    public string $token;
    public string $url;
    private $user;

    public function __construct(){
        $this->user   = Auth::user();
        $this->host   = config('bitrix')['host'];
        $this->token  = config('bitrix')['token'];
        $this->url    = $this->host . $this->token . '/';
    }

    /**
     * Получаем все задачи касаемо авторизованного пользователя
     * @return array
     */
    public function getTasks(): array
    {
        try {
            $bitrixUser  = $this->checkCurrentUserBitrix();

            $link        = $this->host . $this->token . '/' . 'tasks.task.list';
            $apiResponse = Http::get($link, [
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

    public function getLeads($request, $page = 1)
    {
        try {
            $this->checkPositionOfUser(
                (array) [Position::HEAD_RECRUITER_ID, Position::RECRUITER_ID],
                'Позиция пользователя должен быть Старший Рекрутер, Рекрутер, Администратор',
                false
            );

            $link = $this->host . $this->token . 'crm.lead.list';

            $apiResponse = Http::get($link, [
                'filter' => [
                    'CREATED_BY_ID' => 38
                ],
                'start' => ($page - 1) * 50
            ])->json();

            $response = new JsonApiResponse($apiResponse);

            return $response->getData();
        }catch (\DomainException $exception)
        {
            throw new \DomainException($exception);
        }
    }

    /**
     * @param int $userId
     * @param string $message
     * @return bool
     * @throws HttpClientException
     */
    public function addNotification(
        int $userId,
        string $message
    ): bool
    {
        $host   = config('bitrix')['notification']['host'];
        $token  = config('bitrix')['notification']['token'];

        $response = Http::post($host . $token . '/im.notify.system.add.json', [
            'USER_ID' => $userId, //ID пользователя, которому нужно отправить уведомление
            'message' => $message // замените на текст уведомления
        ]);

        if ($response->status() != Response::HTTP_OK)
        {
            throw new HttpClientException($response->reason());
        }

        return true;
    }

    /**
     * @param int $deal_id
     * @return array
     * @throws HttpClientException
     */
    public function getDeal(int $deal_id)
    {
        $response = Http::get($this->url . 'crm.deal.get.json', ['id' => $deal_id]);

        if (!$response->successful()) {
            throw new HttpClientException($response->reason());
        }

        return $response->json()['result'];
    }

    /**
     * @return array
     * @throws HttpClientException
     */
    public function getDeals(array $data)
    {
        $response = Http::get($this->url . 'crm.deal.list.json', $data);

        if (!$response->successful()) {
            throw new HttpClientException($response->reason());
        }

        return $response->json()['result'];
    }

    /**
     * 
     */
    public function getUser(string $search_by, $search)
    {
        $response = Http::get($this->url . 'user.get', [
            'FILTER' => [
                $search_by => $search,
            ]
        ]);

        if (!$response->successful()) {
            throw new HttpClientException($response->reason());
        }

        return $response->json('result');
    }

    /**
     * 
     */
    public function getContact(int $contact_id)
    {
        $response = Http::get($this->url . 'crm.contact.get', [
            'ID' => $contact_id
        ]);

        if (!$response->successful()) {
            throw new HttpClientException($response->reason());
        }

        return $response->json('result');
    }

    


    /**
     * Получем текущего пользователя в Bitrix24
     * @return array
     */
    private function checkCurrentUserBitrix(): array
    {
        try {
            $this->checkPositionOfUser([Position::OPERATOR_ID, Position::INTERN_ID],'Позиция пользователя должен быть выше Оператор, Стажер');

            $user = Auth::user();

            $link = $this->host . $this->token . '/' . 'user.get';

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
     */
    private function checkPositionOfUser(array $positionsId, string $message, bool $expression = true): void
    {
        // $position = User::query()->findOrFail(5263)->positions()->first();
        $position = Auth::user()->position();

        if (in_array($position->id, $positionsId) == $expression)
        {
            throw new \DomainException($message);
        }
    }
}
