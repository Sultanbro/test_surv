<?php

namespace App\Http\Controllers\Signature;

use App\Classes\Helpers\Phone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Files\FileStoreRequest;
use App\Http\Requests\Signature\UCallIntegrationRequest;
use App\Http\Requests\Signature\NewVerificationCodeRequest;
use App\Http\Requests\Signature\VerificationRequest;
use App\Http\Resources\Files\FileResource;
use App\Models\File\File;
use App\Models\Integration\Integration;
use App\Models\SmsCode;
use App\ProfileGroup;
use App\Service\Custom\Files\FileManagerInterface;
use App\Service\Sms\CodeGeneratorInterface;
use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IntegrationController extends Controller
{
    public function setIntegration(UCallIntegrationRequest $request): JsonResponse
    {
        $integration = Integration::query()->updateOrCreate(
            [
                'reference' => 'u-call'
            ],
            [
                'data' => json_encode($request->validated()),
            ]);

        return $this->response('u-call', $integration->toArray());
    }

    public function getIntegration(): JsonResponse
    {
        $integration = Integration::query()->where('reference', 'u-call')->first();
        return $this->response('', $integration);
    }
}
