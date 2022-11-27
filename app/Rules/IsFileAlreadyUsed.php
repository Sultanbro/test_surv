<?php

namespace App\Rules;

use App\Models\File\File;
use Illuminate\Contracts\Validation\Rule;

class IsFileAlreadyUsed implements Rule
{
    const MSG_KEY_FILE_NOT_EXISTS = 'validation.file_not_exists';
    const MSG_KEY_FILE_ALREADY_USED = 'validation.file_already_used';
    const MSG_KEY_FILE_WRONG_EXTENSION = 'validation.file_wrong_extension';

    protected string|null $fileableType;
    protected int|null $fileableId;

    protected array|null $extensions;

    protected string $messageKey;

    public function __construct(string|null $fileableType = null, int|null $fileableId = null)
    {
        $this->fileableType = $fileableType;
        $this->fileableId = $fileableId;

        $this->extensions = null;

        $this->messageKey = self::MSG_KEY_FILE_NOT_EXISTS;
    }

    public function passes($attribute, $value): bool
    {

        $file = File::find($value);

        if (is_null($file)) {
            $this->messageKey = self::MSG_KEY_FILE_NOT_EXISTS;
            return false;
        }

        if (!is_null($file->fileable_id) && !is_null($file->fileable_type)) {

            if ($file->fileable_id === $this->fileableId && $file->fileable_type === $this->fileableType) {
                return true;
            }

            $this->messageKey = self::MSG_KEY_FILE_ALREADY_USED;

            return false;
        }

        if (!is_null($this->extensions)) {
            if (!in_array($file->extension, $this->extensions)) {
                $this->messageKey = self::MSG_KEY_FILE_WRONG_EXTENSION;
                return false;
            }
        }

        return true;
    }

    public function checkExtension(string $extensions): static
    {
        $this->extensions = array_filter(explode('|', $extensions));
        $this->extensions = count($this->extensions) ? $this->extensions : null;
        return $this;
    }

    public function message(): string
    {
        return __($this->messageKey);
    }
}
