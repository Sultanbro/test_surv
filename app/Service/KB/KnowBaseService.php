<?phpnamespace App\Service\KB;use App\KnowBase;use App\Models\KnowBaseModel;use App\User;use Exception;use Illuminate\Support\Collection;class KnowBaseService{    public array $readable_kb_ids = [];    public array $editable_kb_ids = [];    public function __construct()    {        $this->readable_kb_ids = $this->getBooks();        $this->editable_kb_ids = $this->getBooks(2);    }    public static function getAll()    {        return KnowBase::with([            'onlyChildren:id,parent_id,is_category,order,title'        ])->select('id', 'parent_id', 'is_category', 'order', 'title')->get();    }    /**     * Recursively build all tree     */    public function buildAllTree()    {        $items = self::getAll();        foreach ($items as $item) {            if ($item->onlyChildren->isNotEmpty()) {                foreach ($item->onlyChildren as $child) {                    if (in_array($child->id, $this->editable_kb_ids) && $child->is_category == 1) {                        $child->can_edit = true;                    } else {                        $child->can_edit = false;                    }                }            }            if (in_array($item->id, $this->editable_kb_ids) && $item->is_category == 1) {                $item->can_edit = true;            } else {                $item->can_edit = false;            }        }        $itemsById = $items->keyBy('id');        foreach ($items as $item) {            if (!is_null($item->parent_id) && isset($itemsById[$item->parent_id])) {                $parent = $itemsById[$item->parent_id];                $parent->onlyChildren = $parent->onlyChildren ?? collect();                $index = $parent->onlyChildren->search(function ($child) use ($item) {                    return $child->id === $item->id;                });                if ($index !== false) {                    // Remove item if exists in relation, because We have item with its relation and should push this item and avoiding duplicating                    $parent->onlyChildren = $parent->onlyChildren->filter(function ($child) use ($item) {                        return $child->id !== $item->id;                    })->values();                }                $parent->onlyChildren->push($item);                $parent->onlyChildren = $parent->onlyChildren->sortBy('order');            }        }        return $items->sortBy('order')->filter(function ($item) {            return is_null($item->parent_id);        })->values();    }    /**     * Recursively build user tree (user accessible know bases)     *     * @throws Exception     */    public function buildUserTree()    {        // Get all tree        $tree = $this->buildAllTree();        $firstNode = collect([            'id' => 0,            'parent_id' => null,            'onlyChildren' => $tree        ]);        $moves = [];        // Find nodes with its closest parent node  -   ['node' => $id1, 'to' => $id2, 'depth' => $number]        $this->findAccessibleNodes($firstNode, $moves);        $collectionNode = collect([$firstNode]);        foreach ($moves as $move) {            $this->moveNodeAsLastChild($collectionNode, $move['node'], $move['to']);        }        $this->removeNode($tree);        return $tree->values();    }    /**     *  Recursively find nodes with the closest parent     *  Example:     *  user has access to $tree[0] - 1st level accessible     *  user does not have access to $tree[0]['children'][0] - 2nd level inaccessible     *  user has access to $tree[0]['children'][0]['children'][0] - 3rd level accessible     *  So, We need bypass 2nd level, 1st level node is the closest parent for 3rd level node     */    private function findAccessibleNodes($node, &$results, $parentAccess = false, $depth = 0, $closestParentId = null): void    {        // Check direct access or inherited access conditions        $hasAccess = in_array($node['id'], $this->readable_kb_ids);        // If the node has direct access or inherits access, add to results        if ($hasAccess && !$parentAccess && !is_null($closestParentId)) {            $results[] = [                'node' => $node['id'],                'to' => $closestParentId,                'depth' => $depth            ];        } elseif ($hasAccess && !$parentAccess && !is_null($node['parent_id'])) {            $results[] = [                'node' => $node['id'],                'to' => 0,                'depth' => 1            ];        }        if ($hasAccess) $closestParentId = $node['id'];        // Traverse children        foreach ($node['onlyChildren'] as $child) {            if ($child->is_category == 0) continue;            $this->findAccessibleNodes($child, $results, $hasAccess, $depth + 1, $closestParentId);        }    }    private function removeNode(&$nodes): void    {        foreach ($nodes as $key => $node) {            if ($node->is_category == 0) continue;            if (!in_array($node['id'], $this->readable_kb_ids)) {                unset($nodes[$key]);                continue;            }            if (count($node['onlyChildren']) > 0) {                $this->removeNode($node['onlyChildren']);            }        }    }    /**     * @throws Exception     */    private function moveNodeAsLastChild(Collection &$tree, $node1Id, $node2Id): void    {        // Step 1: Detach node1 with its subtree.        [$tree, $node1WithSubtree] = $this->findAndDetachNode($tree, $node1Id);        // Step 2: Insert node1 as the last child of node2.        if (!is_null($node1WithSubtree)) {            $tree = $this->insertAsLastChild($tree, $node2Id, $node1WithSubtree);        } else {            throw new Exception("Node1 not found in the tree.");        }    }    /**     * Recursively searches for a node by ID and detaches it from the tree.     *     * @param Collection $tree     * @param int $nodeId     * @return array An array containing the modified tree and the detached node.     */    private function findAndDetachNode(Collection $tree, int $nodeId): array    {        foreach ($tree as $key => $node) {            if ($node['id'] === $nodeId) {                $detachedNode = $tree->pull($key);                return [$tree->values(), $detachedNode];            } elseif ($node['onlyChildren']->isNotEmpty()) {                [$modifiedChildren, $detachedNode] = $this->findAndDetachNode($node['onlyChildren'], $nodeId);                if (!is_null($detachedNode)) {                    $tree[$key]['onlyChildren'] = $modifiedChildren;                    return [$tree, $detachedNode];                }            }        }        return [$tree, null];    }    /**     * Recursively inserts a node as the last child of the specified parent node.     *     * @param Collection $tree     * @param int $parentId     * @param KnowBase $nodeToInsert     * @return Collection The modified tree.     */    private function insertAsLastChild(Collection $tree, int $parentId, KnowBase $nodeToInsert): Collection    {        return $tree->map(function ($node) use ($parentId, $nodeToInsert) {            if ($node['id'] === $parentId) {                if (!isset($node['onlyChildren'])) {                    $node['onlyChildren'] = collect([]);                }                $node['onlyChildren'] = $node['onlyChildren']->push($nodeToInsert);            } elseif ($node['onlyChildren']->isNotEmpty()) {                $node['onlyChildren'] = $this->insertAsLastChild($node['onlyChildren'], $parentId, $nodeToInsert);            }            return $node;        });    }    /**     * Get knowbase ids that user can see     */    private function getBooks($access = 0): array    {        /** @var User $auth_user */        $auth_user = auth()->user();        $books = [];        if ($auth_user->is_admin == 1) {            $books = KnowBase::query()                ->whereNull('parent_id')                ->orWhere('is_category', 1)                ->get('id')                ->pluck('id')                ->toArray();        } else {            $employee_groups = $auth_user->inGroups()->pluck('id')->toArray();            $supervisor_groups = $auth_user->inGroups(true)->pluck('id')->toArray();            $group_ids = array_unique(array_merge($employee_groups, $supervisor_groups));            $position_id = $auth_user->position_id;            $user_id = auth()->id();            $up = KnowBaseModel::query()                ->where(function ($query) use ($group_ids, $access) {                    $query->where('model_type', 'App\\ProfileGroup')                        ->whereIn('model_id', $group_ids);                    if ($access == 2) $query->where('access', 2);                })                ->orWhere(function ($query) use ($position_id, $access) {                    $query->where('model_type', 'App\\Position')                        ->where('model_id', $position_id);                    if ($access == 2) $query->where('access', 2);                })                ->orWhere(function ($query) use ($user_id, $access) {                    $query->where('model_type', 'App\\User')                        ->where('model_id', $user_id);                    if ($access == 2) $query->where('access', 2);                });            $up = $up->get('book_id')                ->pluck('book_id')                ->toArray();            $books = array_merge($books, $up);            $readOrEditPairs = [];            foreach ($group_ids as $group_id) {                $readOrEditPairs[] = ['position_id' => $auth_user->position_id, 'group_id' => $group_id];            }            $books_with_read_access = KnowBase::withTrashed()                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))                ->whereIn('access', $access == 2 ? [2] : [1, 2])                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('read_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('read_pairs', $pair);                        }                    }                })                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('edit_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('edit_pairs', $pair);                        }                    }                })                ->get('id')->pluck('id')                ->toArray();            $books = array_merge($books, $books_with_read_access);        }        return $books;    }}