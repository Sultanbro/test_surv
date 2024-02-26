<?php

namespace App\Http\Controllers\Signature;

use App\Classes\Helpers\Phone;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Files\FileStoreRequest;
use App\Http\Requests\Signature\NewVerificationCodeRequest;
use App\Http\Requests\Signature\VerificationRequest;
use App\Http\Resources\Files\FileResource;
use App\Http\Resources\SignatureHistoryResource;
use App\Models\File\File;
use App\Models\SmsCode;
use App\ProfileGroup;
use App\Repositories\Signature\SignatureHistoryRepositoryInterface;
use App\Service\Sms\CodeGeneratorInterface;
use App\Service\Sms\ReceiverDto;
use App\Service\Sms\SmsInterface;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SignatureController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorInterface              $codeGenerator,
        private readonly SmsInterface                        $sms,
        private readonly SignatureHistoryRepositoryInterface $signatureHistoryRepository,
    )
    {
    }

    public function list(ProfileGroup $group): AnonymousResourceCollection
    {
        return FileResource::collection($group->files);
    }

    public function histories(User $user): AnonymousResourceCollection
    {
        return SignatureHistoryResource::collection($user->signatureHistories()->with('images')->orderByDesc('created_at')->get());
    }

    public function upload(FileStoreRequest $request, ProfileGroup $group): AnonymousResourceCollection
    {
        $fileName = FileHelper::save($request->validated('file'), 'signature');
        $group->addFile([
            'url' => FileHelper::getUrl('signature', $fileName),
            'local_name' => $fileName,
            'original_name' => $request->validated('original_name', $fileName),
            'original_path' => 'signature'
        ]);
        return FileResource::collection($group->files);
    }

    public function update(FileStoreRequest $request, File $file): FileResource
    {
        $file->update($request->validated());
        return FileResource::make($file);
    }

    public function sendSms(NewVerificationCodeRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        $user->smsCodes()->delete();
        /**
         * @var SmsCode $code
         */
        $code = $user->smsCodes()->create(['code' => $this->codeGenerator->generate()]);
        $receiver = new ReceiverDto(
            Phone::normalize($data['phone']),
            $user->name
        );
        $this->sms->send($receiver, 'Kod podtverzhdeniye dlya podpisaniya dokumenta ' . $code->code);
        $this->signatureHistoryRepository->addHistory($user, $data);

        return $this->response('Отправлен код подтверждение для подписание документа');
    }

    public function delete(File $file): JsonResponse
    {
        $file->delete();
        return $this->response('файл удалень', [], ResponseAlias::HTTP_NO_CONTENT);
    }

    public function verify(VerificationRequest $request, User $user, File $file): JsonResponse
    {
        $sms = $user->smsCodes()->where('code', $request->validated('code'))->first();
        $user->signedFiles()->attach($file, [
            'signed_at' => now()
        ]);
        $sms->delete(); // delete sms verification after using
        return $this->response('verified');
    }

    public function signedFiles(User $user): AnonymousResourceCollection
    {
        $filteredFiles = new Collection();
        $signedFiles = $user->signedFiles()->withTrashed()->get();
        $groups = $user->groups()->withWhereHas('files', fn($query) => $query->withTrashed())->get();
        $groupFiles = new Collection();
        foreach ($groups as $group) {
            $groupFiles->merge($group->files);
        }
        dd($groupFiles);
        foreach ($groupFiles as $file) {
            $signed = $signedFiles->where('id', $file->id)->first();

            if (!$signed && $file->deleted_at) continue;

            $file->signed_at = $signed?->pivot?->signed_at;

            $filteredFiles->add($file);
        }
        return FileResource::collection($filteredFiles);
    }
}
