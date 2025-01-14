<?php

namespace App\Service\Admin;

use App\DTO\Admin\PaperDTO;
use App\Models\Paper;

class PaperService
{
    public static function getAll()
    {
        return Paper::query()->get();
    }

    public function getOne($id)
    {
        return Paper::withTrashed()->find($id);
    }

    /**
     * @throws \Exception
     */
    public function store(PaperDTO $dto)
    {
        // Save image

        return Paper::query()->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'body' => $dto->body,
            'publish' => $dto->publish,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function update($id, PaperDTO $dto)
    {
        $paper = Paper::query()->find($id);

        if ($paper) {
            $paper->update([
                'title' => $dto->title,
                'description' => $dto->description,
                'body' => $dto->body,
                'publish' => $dto->publish,
            ]);

            return $paper;
        } else {
            throw new \Exception('Not found');
        }
    }

    public function delete($id): bool
    {
        Paper::query()->where('id', $id)->delete();
        return true;
    }

    public function search($query): array
    {
        if ($query) {

            return Paper::query()
                ->where('title', 'LIKE', '%' . $query . '%')
                ->orWhere('body', 'LIKE', '%' . $query . '%')
                ->pluck('id')
                ->toArray();
        }

        return [];
    }
}
