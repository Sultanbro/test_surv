<?php

namespace App\Service\Admin;

use App\DTO\Admin\FaqDTO;
use App\Models\Faq;
use Illuminate\Contracts\Support\Arrayable;

class FaqService
{
    public static function getAll()
    {
        return Faq::with([
            'children:id,parent_id,title,page,order'
        ])->select('id', 'parent_id', 'title', 'page', 'order')->get();
    }

    /**
     * Recursively build all tree
     */
    public function buildTree($items, $id = null, $link = 'parent_id')
    {
        return $items->filter(function ($item) use ($id, $link) {
            return $item[$link] === $id;
        })->sortBy('order')->map(function ($item) use ($items, $link) {
            $itemArray = $item instanceof Arrayable ? $item->toArray() : (array)$item;
            $children = $this->buildTree($items, $item['id'], $link);
            if ($children->isNotEmpty()) {
                $itemArray['children'] = $children->values()->all();
            }
            return $itemArray;
        })->values();
    }

    public function getTree()
    {
        return $this->buildTree(self::getAll());
    }

    public function getOne($id)
    {
        return Faq::withTrashed()->find($id);
    }

    /**
     * @throws \Exception
     */
    public function store(FaqDTO $dto)
    {
        return Faq::query()->create([
            'parent_id' => $dto->parent_id,
            'title' => $dto->title,
            'page' => $dto->page,
            'body' => $dto->body,
            'order' => $dto->order,
        ]);
    }

    public function setOrder($items): bool
    {
        foreach ($items as $item) {
            Faq::query()
                ->where('id', $item['id'])
                ->update([
                    'parent_id' => $item['parent_id'],
                    'order' => $item['order']
                ]);
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function update($id, FaqDTO $dto)
    {
        $faq = Faq::query()->find($id);

        if ($faq) {
            $faq->update([
                'parent_id' => $dto->parent_id,
                'title' => $dto->title,
                'page' => $dto->page,
                'body' => $dto->body,
                'order' => $dto->order,
            ]);

            return $faq;
        } else {
            throw new \Exception('Not found');
        }
    }

    public function delete($id)
    {
        Faq::query()->where('id', $id)->delete();
        return true;
    }

    public function search($query): array
    {
        if ($query) {

            return Faq::query()
                ->where('title', 'LIKE', '%' . $query . '%')
                ->orWhere('body', 'LIKE', '%' . $query . '%')
                ->pluck('id')
                ->toArray();
        }

        return [];
    }
}
