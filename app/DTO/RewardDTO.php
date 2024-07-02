<?php

namespace App\DTO;

use App\Models\Award\Award;
use App\Models\Course;
use App\User;
use Illuminate\Http\UploadedFile;

class RewardDTO
{
    public function __construct(
        public int $userId,
        public int|null $courseId,
        public int $awardId,
        public UploadedFile|null $file,
        public UploadedFile|null $preview
    )
    {

    }

    public static function toArray(
        int $userId,
        ?int $courseId,
        int $awardId,
        ?UploadedFile $file,
        ?UploadedFile $preview
    ): self
    {
        $courseId = $courseId ?? null;

        return new self(
            (int) $userId,
            $courseId,
            (int) $awardId,
            $file,
            $preview
        );
    }
}
