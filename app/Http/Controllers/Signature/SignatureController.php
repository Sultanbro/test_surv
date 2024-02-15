<?php

namespace App\Http\Controllers\Signature;

use App\Classes\Helpers\Phone;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Files\FileStoreRequest;
use App\Http\Requests\Signature\NewVerificationCodeRequest;
use App\Http\Requests\Signature\VerificationRequest;
use App\Http\Resources\Files\FileResource;
use App\Models\File\File;
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

class SignatureController extends Controller
{
    public function __construct(
        private readonly FileManagerInterface   $fileManager,
        private readonly CodeGeneratorInterface $codeGenerator,
        private readonly SmsInterface           $sms,
    )
    {
    }

    public function list(ProfileGroup $group): AnonymousResourceCollection
    {
        return FileResource::collection($group->files);
    }

    public function signedFiles(User $user): AnonymousResourceCollection
    {
        $signedFiles = $user->signedFiles()->pluck('id')->toArray();
        $groupFiles = $user->activeGroup()->files()->get();
        foreach ($groupFiles as $file) {
            $file->signed = in_array($file->id, $signedFiles);
        }
        return FileResource::collection($groupFiles);
    }

    public function upload(FileStoreRequest $request, ProfileGroup $group): AnonymousResourceCollection
    {
        $fileName = FileHelper::save($request->validated('file'), 'signature');
        $this->fileManager->apply($request->validated('file'), 'signature');
        $group->addFile([
            'url' => FileHelper::getUrl('signature', $fileName),
            'local_name' => $request->validated('local_name'),
        ]);
        return FileResource::collection($group->files);
    }

    public function update(FileStoreRequest $request, File $file): FileResource
    {
        $file->update($request->validated());
        return FileResource::make($file);
    }

    public function delete(File $file): JsonResponse
    {
        $file->delete();
        return $this->response('файл удалень', [], ResponseAlias::HTTP_NO_CONTENT);
    }

    public function sendSms(NewVerificationCodeRequest $request, User $user): JsonResponse
    {
        /**
         * @var SmsCode $code
         */
        $code = $user->smsCodes()->create(['code' => $this->codeGenerator->generate()]);
        $receiver = new ReceiverDto(
            Phone::normalize($request->validated('phone')),
            $user->name,
        );
        $this->sms->send($receiver, 'код подтверждение для подписание документа ' . $code->code);
        return $this->response('Отправлен код подтверждение для подписание документа');
    }

    public function verify(VerificationRequest $request, User $user, File $file): JsonResponse
    {
        $sms = $user->smsCodes()->where('code', $request->validated('code'))->first();
        $user->signedFiles()->attach($file);
        $sms->delete(); // delete sms verification after using
        return $this->response('verified');
    }


}
