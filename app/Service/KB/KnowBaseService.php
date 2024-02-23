<?phpnamespace App\Service\KB;use App\KnowBase;use App\Models\KnowBaseModel;use App\User;class KnowBaseService{    public array $accessible_kb_ids = [];    public function __construct()    {        $this->accessible_kb_ids = $this->getBooks();    }    public static function getAll()    {        return KnowBase::with([            'onlyChildren:id,parent_id,is_category,order,title'        ])->select('id', 'parent_id', 'is_category', 'order', 'title')->get();    }    public function buildTree()    {        $items = self::getAll();        $itemsById = $items->keyBy('id');        foreach ($items as $item) {            if (!is_null($item->parent_id) && isset($itemsById[$item->parent_id])) {                $parent = $itemsById[$item->parent_id];                $parent->onlyChildren = $parent->onlyChildren ?? collect();                $index = $parent->onlyChildren->search(function ($child) use ($item) {                    return $child->id === $item->id;                });                if ($index !== false) {                    // Remove item if exists in relation, because We have item with its relation and should push this item and avoiding duplicating                    $parent->onlyChildren = $parent->onlyChildren->filter(function ($child) use ($item) {                        return $child->id !== $item->id;                    })->values();                }                $parent->onlyChildren->push($item);                $parent->onlyChildren = $parent->onlyChildren->sortBy('order');            }        }        return $items->sortBy('order')->filter(function ($item) {            return is_null($item->parent_id);        })->values();    }    /**     * This function might be used filter all tree by checking user permissions...((     */    public function filterTree($tree, &$accessibleParents = null) {        if (is_null($accessibleParents)) {            $accessibleParents = collect();        }        foreach ($tree as $key => $node) {            if (!$this->hasAccess($node->id) && ($node->is_category == 1 || $node->parent_id == null)) {                $node->can_view = false;//                dd($node);                if (!empty($node->onlyChildren)) {                    $this->filterTree($node->onlyChildren, $accessibleParents);                }            } else {                $node->can_view = true;                if (!empty($node->onlyChildren)) {                    $node->onlyChildren = $this->filterTree($node->onlyChildren)->sortBy('order');                }                $accessibleParents->push($node);            }        }        return $accessibleParents->sortBy('order')->values();    }    public function hasAccess($itemId): bool    {        $can_read = false;        if(auth()->user()->can('kb_edit')) {            $can_read = true;        } else if(in_array($itemId, $this->accessible_kb_ids)) {            $can_read = true;        }        return $can_read;    }    /**     * Get knowbase ids that user can see     */    private function getBooks($access = 0) : array    {        /** @var User $auth_user */        $auth_user = auth()->user();        $books = [];        if($auth_user->is_admin == 1)  {            $books = KnowBase::query()                ->whereNull('parent_id')                ->orWhere('is_category', 1)                ->get('id')                ->pluck('id')                ->toArray();        } else {            $employee_groups = $auth_user->inGroups()->pluck('id')->toArray();            $supervisor_groups = $auth_user->inGroups(true)->pluck('id')->toArray();            $group_ids = array_unique(array_merge($employee_groups, $supervisor_groups));            $position_id =  $auth_user->position_id;            $user_id =  auth()->id();            $up = KnowBaseModel::query()                ->where(function($query) use ($group_ids, $access) {                    $query->where('model_type', 'App\\ProfileGroup')                        ->whereIn('model_id', $group_ids);                    if($access == 2) $query->where('access', 2);                })                ->orWhere(function($query) use ($position_id, $access) {                    $query->where('model_type', 'App\\Position')                        ->where('model_id', $position_id);                    if($access == 2) $query->where('access', 2);                })                ->orWhere(function($query) use ($user_id, $access) {                    $query->where('model_type', 'App\\User')                        ->where('model_id', $user_id);                    if($access == 2) $query->where('access', 2);                });            $up = $up->get('book_id')                ->pluck('book_id')                ->toArray();            $books = array_merge($books, $up);            $readOrEditPairs = [];            foreach ($group_ids as $group_id) {                $readOrEditPairs[] = ['position_id' => $auth_user->position_id, 'group_id' => $group_id];            }            $books_with_read_access =  KnowBase::withTrashed()                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))                ->whereIn('access', $access == 2 ? [2] : [1,2])                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('read_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('read_pairs', $pair);                        }                    }                })                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('edit_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('edit_pairs', $pair);                        }                    }                })                ->get('id')->pluck('id')                ->toArray();            $books = array_merge($books, $books_with_read_access);        }        return $books;    }}