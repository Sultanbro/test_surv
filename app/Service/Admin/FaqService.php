<?php

namespace App\Service\Admin;

use App\Classes\Helpers\Currency;
use App\Downloads;
use App\DTO\Admin\FaqDTO;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Analytics\TraineeReport;
use App\Models\Analytics\UserStat;
use App\Models\Faq;
use App\Models\GroupUser;
use App\Photo;
use App\Position;
use App\PositionDescription;
use App\QualityRecordWeeklyStat;
use App\Zarplata;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

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
    public function buildTree($items, $id = null, $link = 'parent_id') {
        return $items->filter(function ($item) use ($id, $link) {
            return $item[$link] === $id;
        })->sortBy('order')->map(function ($item) use ($items, $link) {
            $itemArray = $item instanceof Arrayable ? $item->toArray() : (array) $item;
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
        $parent = Faq::query()->where('id', $dto->parent_id)->exists();
        if ($parent) {
            return Faq::query()->create([
                'parent_id' => $dto->parent_id,
                'title' => $dto->title,
                'page' => $dto->page,
                'body' => $dto->body,
                'order' => $dto->order,
            ]);
        } else {
            throw new \Exception('Parent faq not found');
        }
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
}
