<?php

namespace App\Http\Controllers\PromoCode;

use App\Actions\PromoCode\DeleteAction\DeletePromoCodeAction;
use App\Actions\PromoCode\DeleteAction\DeletePromoCodeDto;
use App\Actions\PromoCode\SaveAction\SavePromoCodeAction;
use App\Actions\PromoCode\SaveAction\SavePromoCodeDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\SavePromoCodeRequest;
use App\Repositories\PromoCode\PromoCodeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function __construct(
        public PromoCodeRepositoryInterface $promoCodeRepository,
        public SavePromoCodeAction          $savePromoCodeAction,
        public DeletePromoCodeAction        $deletePromoCodeAction,
    )
    {
    }

    public function get(): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: $this->promoCodeRepository->getAllValidPromoCodes()
        );
    }

    public function save(SavePromoCodeRequest $request): JsonResponse
    {
        $this->savePromoCodeAction->save(SavePromoCodeDto::fromRequest($request));
        return $this->response(
            message: 'success',
            code: 201
        );
    }

    public function destroy(Request  $request): JsonResponse
    {
        $this->deletePromoCodeAction->delete(new DeletePromoCodeDto($request->get('code')));
        return $this->response(
            message: 'success',
            code: 204
        );
    }
}
