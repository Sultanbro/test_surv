<?php

namespace App\Repositories\Signature;

use App\Helpers\FileHelper;
use App\Models\UserSignatureHistory;
use App\User;

class SignatureHistoryRepository implements SignatureHistoryRepositoryInterface
{
    public function addHistory(User $user, array $data): void
    {
        $images = $data['images'] ?? [];
        unset($data['images']);

        /** @var UserSignatureHistory $signature */
        $signature = $user->signatureHistories()->create($data);

        if (empty($images)) {
            /** @var UserSignatureHistory $lastHistory */
            $lastHistory = $user->lastSignatureHistory()->first();
            foreach ($lastHistory->images->toArray() as $image) {
                $signature->addImage([
                    'url' => $image->url,
                    'local_name' => $image->local_name,
                    'original_name' => $image->original_name
                ]);
            }
        } else {
            foreach ($images as $image) {
                $fileName = FileHelper::save($image, 'signature/history');
                $signature->addImage([
                    'url' => FileHelper::getUrl('signature/history/', $fileName),
                    'local_name' => $fileName,
                    'original_name' => $fileName
                ]);
            }
        }
    }
}