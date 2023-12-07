<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Contracts\CourseInterface;
use App\Models\KnowBaseModel;
use Illuminate\Support\Facades\DB;

class KnowBase extends Model implements CourseInterface
{
    use SoftDeletes;

    protected $table = 'kb';

    public $timestamps = true;

    protected $appends = ['opened'];

    protected $casts = [
        'read_pairs' => 'array',
        'edit_pairs' => 'array'
    ];

    protected $fillable = [
        'parent_id',
        'title',
        'user_id', // author
        'editor_id', //
        'text',
        'is_deleted',
        'order',
        'pass_grade',
        'hash', // уникальная ссылка чтобы поделиться
        'access', // доступ   0 - никто, 1 - к просмотру,  2 - к редактированию,
        'read_pairs',
        'edit_pairs',
        'is_category'
    ];


    public function questions()
    {
        return $this->morphMany('App\Models\TestQuestion', 'testable');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order')
            ->with('children', 'questions');
    }

    public function item_model()
    {
        return $this->hasOne('App\Models\CourseItemModel', 'item_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getOpenedAttribute()
    {
        return false;
    }

    public static function getTopParent($id)
    {
        $kb = self::withTrashed()->find($id);
        if ($kb && $kb->parent_id != null) {
            return self::getTopParent($kb->parent_id);
        }
        return $kb;
    }

    public static function getArray(&$arr, $kb)
    {
        foreach ($kb->children as $key => $child) {
            $arr[] = [
                'id' => $child->id,
                'parent_id' => $child->parent_id,
                'title' => $child->title,
                'user_id' => $child->user_id,
                'editor_id' => $child->editor_id,
                'text' => $child->text,
                'is_deleted' => $child->is_deleted,
                'order' => $child->order,
                'hash' => $child->hash,
            ];

            self::getArray($arr, $child);
        }
    }

    public static function getAllChildrenIds($parentId, &$result = [])
    {
        $children = KnowBase::where('parent_id', $parentId)->get();

        foreach ($children as $child) {
            $result[] = $child->id;
            self::getAllChildrenIds($child->id, $result);
        }

        return $result; // Return the accumulated result array
    }

    public static function getRandomPage()
    {

        $corp_book_ids = self::getBooks(); // книги в группе
        if (count($corp_book_ids) == 0) return null;
        if (auth()->user()->is_admin !== 1) {
            $book_ids = [];
            foreach (array_unique($corp_book_ids) as $book_id) {
                $book_ids[] = self::getAllChildrenIds($book_id);
            }

            $corp_book_ids = [];
            foreach ($book_ids as $innerArray) {
                $corp_book_ids = array_merge($corp_book_ids, $innerArray);
            }
        }
        $books = KnowBase::query()
            ->with('questions')
            ->where('text', '!=', '')
            ->whereNotNull('text')
            ->whereIn('id', $corp_book_ids)
            ->get();

        return $books->count() > 0 ? $books->random() : null;
    }

    public function getUsersWithAccess()
    {
        $items = \App\Models\KnowBaseModel::where('book_id', $this->id)->get();

        $arr = [];

        foreach ($items as $key => $item) {

            if ($item->model_type == 'App\\User') {
                $arr[] = $item->model_id;
            }

            if ($item->model_type == 'App\\ProfileGroup') {
                $group = \App\ProfileGroup::find($item->model_id);
                if (!$group) continue;
                $arr = array_merge($arr, json_decode($group->users));
            }

            if ($item->model_type == 'App\\Position') {
                $users = \App\User::where('position_id', $item->model_id)->get('id')->pluck('id')->toArray();
                if (!$users) continue;
                $arr = array_merge($arr, $users);
            }

        }

        return array_unique($arr);
    }

    private static function getBooks($access = 0, User $user = null)
    {

        $user = $user ?? auth()->user();

        $books = [];
        if ($user->is_admin == 1) {
            $books = KnowBase::get('id')->pluck('id')->toArray();
        } else {

            $groups = $user->inGroups();
            $group_ids = collect($groups)->pluck('id')->toArray();
            $position_id = $user->position_id;
            $user_id = $user->id;

            $up = KnowBaseModel::query()
                ->where(function ($query) use ($group_ids, $access) {
                    $query->where('model_type', 'App\\ProfileGroup')
                        ->whereIn('model_id', $group_ids);
                    if ($access == 2) $query->where('access', 2);
                })
                ->orWhere(function ($query) use ($position_id, $access) {
                    $query->where('model_type', 'App\\Position')
                        ->where('model_id', $position_id);
                    if ($access == 2) $query->where('access', 2);
                })
                ->orWhere(function ($query) use ($user_id, $access) {
                    $query->where('model_type', 'App\\User')
                        ->where('model_id', $user_id);
                    if ($access == 2) $query->where('access', 2);
                });

            $up = $up->get('book_id')
                ->pluck('book_id')
                ->toArray();

            $books = array_merge($books, $up);

            $books_with_read_access = KnowBase::withTrashed()
                ->whereIn('access', $access == 2 ? [2] : [1, 2])
                ->get('id')->pluck('id')
                ->toArray();

            $books = array_merge($books, $books_with_read_access);
        }


        return $books;
    }

    /**
     * CourseInterface
     * @param mixed $id
     * @param mixed $items
     *
     * @return [type]
     */
    public function pluckArticles($items)
    {
        $arr = [];

        foreach ($items as $key => $item) {
            $arr[] = $item->id;
            $arr = array_merge($arr, $this->pluckArticles($item->children));
        }

        return $arr;
    }

    /**
     * CourseInterface
     * @return [type]
     */
    public function getOrder()
    {
        $kb = self::with('children')->find($this->id);

        return $this->pluckArticles($kb->children);
    }

    /**
     * CourseInterface
     *
     * @param mixed $id
     *
     * @return [type]
     */
    public function nextElement($id)
    {
        $arr = $this->getOrder();
        $key = array_search($id, $arr);
        return $key && $key + 1 <= count($arr) - 1 ? $arr[$key + 1] : null;
    }

    public function scopeSearchChildrenIdsByKbId($query, $id)
    {
        if ($id) {
            $descendantIds = DB::select("
            WITH RECURSIVE descendant_cte AS (
                SELECT id, parent_id
                FROM kb
                WHERE parent_id = :parent_id
                UNION ALL
                SELECT kb.id, kb.parent_id
                FROM kb
                INNER JOIN descendant_cte ON kb.parent_id = descendant_cte.id
            )
            SELECT id FROM descendant_cte", ['parent_id' => $id]
            );
            $childIds = [];
            foreach ($descendantIds as $item) {
                $childIds[] = $item->id;
            }
            return $query->whereIn('id', $childIds);
        }
        return $query;
    }

    /**
     * Get top parent id with one query
     */
    public function getTopParentV2($id)
    {
        return DB::select("
            WITH RECURSIVE ParentChain AS (
                SELECT id, parent_id, is_category
                FROM kb
                WHERE id = :child_id
            
                UNION ALL
            
                SELECT k.id, k.parent_id, k.is_category
                FROM kb k
                INNER JOIN ParentChain pc ON k.id = pc.parent_id
            )
            SELECT id
            FROM ParentChain
            WHERE parent_id IS NULL OR is_category = 1
            LIMIT 1", ['child_id' => $id]
        )[0]->id;
    }
}
