<?phpnamespace App\Service\KB;use App\KnowBase;use App\Models\KnowBaseModel;use App\User;use Closure;use Exception;use Illuminate\Support\Collection;class KnowBaseService{    public array $accessible_kb_ids = [];    public function __construct()    {        $this->accessible_kb_ids = array_merge($this->getBooks(), []);    }    public static function getAll()    {        return KnowBase::with([            'onlyChildren:id,parent_id,is_category,order,title'        ])->select('id', 'parent_id', 'is_category', 'order', 'title')->get();    }    public function buildTree()    {        $items = self::getAll();        $itemsById = $items->keyBy('id');        foreach ($items as $item) {            if (!is_null($item->parent_id) && isset($itemsById[$item->parent_id])) {                $parent = $itemsById[$item->parent_id];                $parent->onlyChildren = $parent->onlyChildren ?? collect();                $index = $parent->onlyChildren->search(function ($child) use ($item) {                    return $child->id === $item->id;                });                if ($index !== false) {                    // Remove item if exists in relation, because We have item with its relation and should push this item and avoiding duplicating                    $parent->onlyChildren = $parent->onlyChildren->filter(function ($child) use ($item) {                        return $child->id !== $item->id;                    })->values();                }                $parent->onlyChildren->push($item);                $parent->onlyChildren = $parent->onlyChildren->sortBy('order');            }        }        $tree = $items->sortBy('order')->filter(function ($item) {            return is_null($item->parent_id);        })->values();        $moves = [];//return $tree;        $firstNode = collect([            'id' => 0,            'parent_id' => null,            'onlyChildren' => $tree        ]);//        return $tree;//        $this->test($tree);        $this->findAccessibleNodes($firstNode, $moves);        // ["node" => node_id, "to" => closest_parent_id, "depth" => depth_between_node_and_to]//        dd($moves);        foreach ($moves as $move) {            $this->moveNodeAsLastChild($tree, $move['node'], $move['to']);        }//        return $tree->values();        $this->removeNode($tree);        return $tree->values();//        dd($this->filterTree($tree)[0]->onlyChildren);    }    private function findAccessibleNodes($node, &$results, $parentAccess = false, $depth = 0, $closestParentId = null)    {        // Check direct access or inherited access conditions        $hasAccess = in_array($node['id'], $this->accessible_kb_ids);        // If the node has direct access or inherits access, add to results        if ($hasAccess && !$parentAccess && !is_null($closestParentId)) {            $results[] = [                'node' => $node['id'],                'to' => $closestParentId,                'depth' => $depth            ];        } elseif ($hasAccess && !$parentAccess && !is_null($node['parent_id'])) {            $results[] = [                'node' => $node['id'],                'to' => 0,                'depth' => 1            ];        }//        if ($node['id'] == 2349) {//            dd($hasAccess . " " . $parentAccess . " " . $closestParentId);//        }        if ($hasAccess) $closestParentId = $node['id'];        // Traverse children        foreach ($node['onlyChildren'] as $child) {            if ($child->is_category == 0) continue;            $this->findAccessibleNodes($child, $results, $hasAccess, $depth + 1, $closestParentId);        }    }    private function removeNode(&$nodes)    {        foreach ($nodes as $key => $node) {            if ($node->is_category == 0) continue;            // If the current node is the one to remove            if (!in_array($node['id'], $this->accessible_kb_ids)) {                // Remove the node                unset($nodes[$key]);                continue; // Assuming you only need to remove one specific node and its children            }            if (count($node['onlyChildren']) > 0) {                $this->removeNode($nodes[$key]['onlyChildren']);            }        }    }    private function captureAccessDetails(Collection $tree, $closestAccessibleParentId = null, $currentDepth = 0, array &$results = [], $depth = 0, $parentAccess = false)    {        foreach ($tree as $node) {            $currentNodeId = $node['id'];            // Determine if the node inherently has access based on its properties or its parent's access.            $isAccessible = ($node['is_category'] == 1 || $node['parent_id'] == null) ? $this->hasAccess($node->id) : $parentAccess;            if ($isAccessible) {                // If there's a previously marked accessible parent and the current node also has access, capture it.                if (!is_null($closestAccessibleParentId)) {                    $results[] = [                        'node' => $currentNodeId,                        'to' => $closestAccessibleParentId,                        'depth' => $currentDepth,                    ];                }                $closestAccessibleParentId = $currentNodeId; // Update the closest accessible parent ID for its subtree.                $currentDepth = 0; // Reset depth since this node is accessible.            } else {                // For nodes without inherent access, increase the depth from the closest accessible parent.                $currentDepth++;            }            // Continue the traversal if the node has children, passing along the updated access status.            if (!empty($node['children'])) {                $this->captureAccessDetails($node['children'], $closestAccessibleParentId, $currentDepth, $results, $depth + 1, $isAccessible);            }        }        usort($results, function ($item1, $item2) {            return $item1['depth'] <=> $item2['depth'];        });        return $results;    }    public function test($tree)    {        foreach ($tree as $node) {            $this->processNode($node);        }    }    private function processNode($node, $parentAccess = false, $closestAccessibleParent = null)    {        // Determine access for the current node        $nodeAccess = ($node->is_category == 1 || $node->parent_id == null) ? $this->hasAccess($node->id) : $parentAccess;        // Update the closest accessible parent if the current node has access        if ($nodeAccess && !$parentAccess && !is_null($closestAccessibleParent)) {            $this->move[] = [                'node' => $node->id,                'to' => $closestAccessibleParent->id            ];        }        // Process children nodes if any        if ($node->onlyChildren->isNotEmpty()) {            if ($nodeAccess) $closestAccessibleParent = $node;            $node->onlyChildren = $node->onlyChildren->map(function ($child) use ($node, $nodeAccess, $closestAccessibleParent) {                $nodeAccess = ($node->is_category == 1 || $node->parent_id == null) ? $this->hasAccess($node->id) : $nodeAccess;                if ($nodeAccess && !$nodeAccess && !is_null($closestAccessibleParent)) {                    $this->move[] = [                        'node' => $node->id,                        'to' => $closestAccessibleParent->id                    ];                }                return $this->processNode($child, $nodeAccess, $closestAccessibleParent);            })->values();        }        return $node; // Return the processed node    }    /**     * @throws Exception     */    private function moveNodeAsLastChild(Collection &$tree, $node1Id, $node2Id)    {        // Step 1: Detach node1 with its subtree.        [$tree, $node1WithSubtree] = $this->findAndDetachNode($tree, $node1Id);        // Step 2: Insert node1 as the last child of node2.        if (!is_null($node1WithSubtree)) {            $tree = $this->insertAsLastChild($tree, $node2Id, $node1WithSubtree);        } else {            throw new Exception("Node1 not found in the tree.");        }    }    /**     * Recursively searches for a node by ID and detaches it from the tree.     *     * @param Collection $tree     * @param int $nodeId     * @return array An array containing the modified tree and the detached node.     */    private function findAndDetachNode(Collection $tree, $nodeId): array    {        foreach ($tree as $key => $node) {            if ($node['id'] === $nodeId) {                $detachedNode = $tree->pull($key);                return [$tree->values(), $detachedNode];            } elseif ($node['onlyChildren']->isNotEmpty()) {                [$modifiedChildren, $detachedNode] = $this->findAndDetachNode($node['onlyChildren'], $nodeId);                if (!is_null($detachedNode)) {                    $tree[$key]['onlyChildren'] = $modifiedChildren;                    return [$tree, $detachedNode];                }            }        }        return [$tree, null];    }    /**     * Recursively inserts a node as the last child of the specified parent node.     *     * @param Collection $tree     * @param int $parentId     * @param KnowBase $nodeToInsert     * @return Collection The modified tree.     */    private function insertAsLastChild(Collection $tree, int $parentId, KnowBase $nodeToInsert): Collection    {        return $tree->map(function ($node) use ($parentId, $nodeToInsert) {            if ($node['id'] === $parentId) {                if (!isset($node['onlyChildren'])) {                    $node['onlyChildren'] = collect([]);                }                $node['onlyChildren'] = $node['onlyChildren']->push($nodeToInsert);            } elseif ($node['onlyChildren']->isNotEmpty()) {                $node['onlyChildren'] = $this->insertAsLastChild($node['onlyChildren'], $parentId, $nodeToInsert);            }            return $node;        });    }    /**     * This function might be used filter all tree by checking user permissions...((     */    public function filterTree($tree, &$accessibleParents = null)    {        if (is_null($accessibleParents)) {            $accessibleParents = collect();        }        foreach ($tree as $key => $node) {            if (!$this->hasAccess($node->id) && ($node->is_category == 1 || $node->parent_id == null)) {                $node->can_view = false;//                dd($node);                if (!empty($node->onlyChildren)) {                    $this->filterTree($node->onlyChildren, $accessibleParents);                }            } else {                $node->can_view = true;                if (!empty($node->onlyChildren)) {                    $node->onlyChildren = $this->filterTree($node->onlyChildren)->sortBy('order');                }                $accessibleParents->push($node);            }        }        return $accessibleParents->sortBy('order')->values();    }    public function hasAccess($itemId): bool    {        $can_read = false;        if (auth()->user()->can('kb_edit')) {            $can_read = true;        } else if (in_array($itemId, $this->accessible_kb_ids)) {            $can_read = true;        }        return $can_read;    }    /**     * Get knowbase ids that user can see     */    private function getBooks($access = 0): array    {        /** @var User $auth_user */        $auth_user = auth()->user();        $books = [];        if ($auth_user->is_admin == 1) {            $books = KnowBase::query()                ->whereNull('parent_id')                ->orWhere('is_category', 1)                ->get('id')                ->pluck('id')                ->toArray();        } else {            $employee_groups = $auth_user->inGroups()->pluck('id')->toArray();            $supervisor_groups = $auth_user->inGroups(true)->pluck('id')->toArray();            $group_ids = array_unique(array_merge($employee_groups, $supervisor_groups));            $position_id = $auth_user->position_id;            $user_id = auth()->id();            $up = KnowBaseModel::query()                ->where(function ($query) use ($group_ids, $access) {                    $query->where('model_type', 'App\\ProfileGroup')                        ->whereIn('model_id', $group_ids);                    if ($access == 2) $query->where('access', 2);                })                ->orWhere(function ($query) use ($position_id, $access) {                    $query->where('model_type', 'App\\Position')                        ->where('model_id', $position_id);                    if ($access == 2) $query->where('access', 2);                })                ->orWhere(function ($query) use ($user_id, $access) {                    $query->where('model_type', 'App\\User')                        ->where('model_id', $user_id);                    if ($access == 2) $query->where('access', 2);                });            $up = $up->get('book_id')                ->pluck('book_id')                ->toArray();            $books = array_merge($books, $up);            $readOrEditPairs = [];            foreach ($group_ids as $group_id) {                $readOrEditPairs[] = ['position_id' => $auth_user->position_id, 'group_id' => $group_id];            }            $books_with_read_access = KnowBase::withTrashed()                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))                ->whereIn('access', $access == 2 ? [2] : [1, 2])                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('read_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('read_pairs', $pair);                        }                    }                })                ->orWhere(function ($query) use ($readOrEditPairs) {                    if (count($readOrEditPairs) > 0) {                        $query->whereJsonContains('edit_pairs', $readOrEditPairs[0]);                        foreach ($readOrEditPairs as $pair) {                            $query->orWhereJsonContains('edit_pairs', $pair);                        }                    }                })                ->get('id')->pluck('id')                ->toArray();            $books = array_merge($books, $books_with_read_access);        }        return $books;    }}