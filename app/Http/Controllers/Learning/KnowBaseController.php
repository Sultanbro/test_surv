<?php

namespace App\Http\Controllers\Learning;

use App\Http\Resources\KB\KnowBaseTreeResource;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Service\KB\KnowBaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\CourseItemModel;
use Illuminate\Http\Request;
use App\Models\KnowBaseModel;
use App\Models\TestQuestion;
use App\UserNotification;
use App\ProfileGroup;
use Carbon\Carbon;
use App\KnowBase;
use App\Position;
use App\Setting;
use App\User;
use Auth;

class KnowBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        View::share('menu', 'kb');
        View::share('link', 'kb');

        return view('admin.books');
    }

    public function get(): array
    {
        $books = KnowBase::query()
            ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))
            ->orderBy('order');

        if (!auth()->user()->can('kb_edit')) $books->whereIn('id', $this->getBooks());


        return [
            'books' => $books->get()->toArray()
        ];
    }

    /**
     * Get knowbase ids that user can see
     */
    private function getBooks($access = 0): array
    {
        /** @var User $auth_user */
        $auth_user = auth()->user();
        $books = [];
        if ($auth_user->is_admin == 1) {
            $books = KnowBase::query()
                ->whereNull('parent_id')
                ->orWhere('is_category', 1)
                ->get('id')
                ->pluck('id')
                ->toArray();
        } else {

            $employee_groups = $auth_user->inGroups()->pluck('id')->toArray();
            $supervisor_groups = $auth_user->inGroups(true)->pluck('id')->toArray();
            $group_ids = array_unique(array_merge($employee_groups, $supervisor_groups));

            $position_id = $auth_user->position_id;
            $user_id = auth()->id();

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

            $readOrEditPairs = [];
            foreach ($group_ids as $group_id) {
                $readOrEditPairs[] = ['position_id' => $auth_user->position_id, 'group_id' => $group_id];
            }
            $books_with_read_access = KnowBase::withTrashed()
                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))
                ->whereIn('access', $access == 2 ? [2] : [1, 2])
                ->orWhere(function ($query) use ($readOrEditPairs) {
                    if (count($readOrEditPairs) > 0) {
                        $query->whereJsonContains('read_pairs', $readOrEditPairs[0]);
                        foreach ($readOrEditPairs as $pair) {
                            $query->orWhereJsonContains('read_pairs', $pair);
                        }
                    }
                })
                ->orWhere(function ($query) use ($readOrEditPairs) {
                    if (count($readOrEditPairs) > 0) {
                        $query->whereJsonContains('edit_pairs', $readOrEditPairs[0]);
                        foreach ($readOrEditPairs as $pair) {
                            $query->orWhereJsonContains('edit_pairs', $pair);
                        }
                    }
                })
                ->get('id')->pluck('id')
                ->toArray();

            $books = array_merge($books, $books_with_read_access);
        }

        return $books;
    }

    /**
     * Search knowbases that contains $phrase
     */
    public function search(Request $request): array
    {
        $kbs = $this->getBooks();
        $allIds = [];
        foreach ($kbs as $kb) {
            $children = KnowBase::getAllChildrenIdsByKbId($kb);
            $allIds = array_merge($allIds, $children);
        }
        $phrase = '%' . $request->text . '%';
        $items = KnowBase::query()
            ->where(function ($q) use ($phrase) {
                $q->where('title', 'like', $phrase)
                    ->orWhere('text', 'like', $phrase);
            })
            ->whereIn('id', $allIds)
            ->searchChildrenIdsByKbId($request->id)
            ->orderBy('order')
            ->limit(10)
            ->get();


        foreach ($items as $item) {
            $item->text = $this->cutFragment($item->text, $request->text);
            $item->book = $this->getTopParent($item->id);
        }

        return [
            'items' => $items,
        ];
    }

    /**
     * Cut long text to search results
     */
    private function cutFragment($text, $fragment): string
    {
        $plainText = strip_tags($text);

        $start_pos = mb_stripos($plainText, $fragment);
        $start_pos -= 15;
        if ($start_pos < 0) {
            $start_pos = 0;
        }

        return '...' . mb_substr($plainText, $start_pos, 50) . '...';
    }

    /**
     * Get archived knowbases
     */
    public function getArchived(): array
    {
        if (auth()->user()->can('kb_edit')) {
            $books = KnowBase::onlyTrashed()
                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))
                ->orderBy('order')
                ->get()
                ->toArray();
        } else {
            $books = [];
        }

        return [
            'books' => $books,
        ];
    }

    /**
     * Get children of know base
     */
    public function getTree(Request $request): array
    {
        $course_item_id = $request->get('course_item_id');

        $trees = [];
        $book = null;

        $can_read = false;
        if (auth()->user()->can('kb_edit')) {
            $can_read = true;
        } else if (in_array($request->get('id'), $this->getBooks())) {
            $can_read = true;
        }

        $favouriteIds = [];

        if ($can_read || ($request->has('can_read') && $request->get('can_read'))) {
            $trees = KnowBase::where('parent_id', $request->get('id'))
                ->with('children')
                ->with('questions')
                ->orderBy('order')
                ->get();

            $children_ids = KnowBase::query()->searchChildrenIdsByKbId($request->id)->pluck('id')->toArray();
            $children_ids[] = $request->get('id');
            $favouriteIds = \DB::table('user_starred_kbs')->where('user_id', Auth::id())->whereIn('kb_id', $children_ids)->pluck('kb_id')->toArray();


            $trees->toArray();

            /** @var KnowBase $book */
            $book = KnowBase::query()
                ->where(fn($query) => $query->whereNull('parent_id')->orWhere('is_category', 1))
                ->orderBy('order')
                ->where('id', $request->get('id'))
                ->first();


            if ($book) $book->access = in_array($book->id, $this->getBooks(2)) ? 2 : 1;

        }

        $item_models = [];
        if ($book) {
            $kb_ids = $book->getOrder();

            $item_models = CourseItemModel::query()->whereIn('item_id', $kb_ids)
                ->where('type', 3)
                ->where('user_id', auth()->id())
                ->where('course_item_id', $course_item_id)
                ->get();
        }

        return [
            'trees' => $trees,
            'favourite_ids' => $favouriteIds,
            'book' => $book,
            'item_models' => $item_models,
            'can_save' => $this->canSaveWithoutTest()
        ];
    }

    public function getAllTree(KnowBaseService $service): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: KnowBaseTreeResource::collection($service->buildAllTree())
        );
    }

    /**
     * @throws \Exception
     */
    public function getUserTree(KnowBaseService $service): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: KnowBaseTreeResource::collection($service->buildUserTree())
        );
    }

    /**
     * get page of knowbase
     */
    public function getPage(Request $request): array
    {
        $course_item_id = $request->get('course_item_id');
        $user_id = auth()->id();

        /** @var KnowBase $page */
        $page = KnowBase::withTrashed()
            //->with('questions')
            ->with('questions.result', function ($query) use ($course_item_id, $user_id) {
                $query->where('course_item_model_id', $course_item_id)
                    ->where('user_id', $user_id);
            })
            ->find($request->id);

        $page->item_model = CourseItemModel::where('user_id', $user_id)
            ->where('type', 3)
            ->where('item_id', $page->id)
            ->where('course_item_id', 0)
            ->first();

        /** @var User $author */
        $author = User::withTrashed()->find($page->user_id);
        /** @var User $editor */
        $editor = User::withTrashed()->find($page->editor_id);

        $page->author = $author ? $author->last_name . ' ' . $author->name : 'Неизвестный';
        $page->editor = $editor ? $editor->last_name . ' ' . $editor->name : 'Неизвестный';
        $page->edited_at = Carbon::parse($page->updated_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
        $page->created = Carbon::parse($page->created_at)->setTimezone('Asia/Almaty')->format('d.m.Y H:i');
        $page->questions = TestQuestion::where('testable_type', 'App\Knowbase')->where('testable_id', $request->id)->get();
        $page->editor_avatar = $editor ? 'users_img/' . $editor->img_url : 'images/avatar.png';


        $breadcrumbs = $this->getBreadcrumbs($page);
        $top_parent = $this->getTopParent($request->id);

        $trees = [];

        if ($request->get('refresh')) {
            if ($top_parent) {
                $trees = KnowBase::query()->where('parent_id', $top_parent->id)->with('children')
                    ->orderBy('order')->get();

                foreach ($trees as $tree) {
                    $tree->parent_id = null;
                }
                $trees = $trees->toArray();
            }
        }


        $can_read = false;
        if ($top_parent != null && auth()->user()->can('kb_edit')) {
            $can_read = true;
        } else if ($top_parent != null && in_array($top_parent->id, $this->getBooks())) {
            $can_read = true;
        } else if ($course_item_id != 0) {
            $can_read = true;
        }

        if ($can_read) {
            $data = [
                'book' => $page,
                'breadcrumbs' => $breadcrumbs,
                'tree' => $trees,
                'top_parent' => $top_parent,
            ];
        } else {
            $data = [
                'book' => null,
                'breadcrumbs' => [],
                'tree' => [],
                'top_parent' => null,
            ];
        }

        return $data;
    }

    /**
     * find top parent of knowbase // Or find parent where is_category = 1
     * @param $id
     * @return Knowbase | null
     */
    private function getTopParent($id): ?KnowBase
    {
        /** @var KnowBase $kb */
        $kb = KnowBase::withTrashed()->find($id);
        if ($kb->is_category == 1 || $kb->parent_id == null) {
            return $kb;
        }
        return $this->getTopParent($kb->parent_id);
    }

    /**
     * Breadcrumbs of page
     */
    private function getBreadcrumbs($page): array
    {
        $breadcrumbs = [];
        $breadcrumbs[] = $page;
        $parent_id = $page->parent_id;

        while ($parent_id != null) {
            /** @var KnowBase $xpage */
            $xpage = KnowBase::withTrashed()->find($parent_id);
            if ($xpage) {
                $breadcrumbs[] = $xpage;
                $parent_id = $xpage->parent_id;
            } else {
                $parent_id = null;
            }
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * Update access to knowbase
     * update knowbase
     */
    public function updateSection(Request $request): void
    {
        /** @var KnowBase $page */
        $page = KnowBase::query()->find($request->get('id'));
        if ($page) {
            $page->title = $request->title;
            $page->editor_id = Auth::user()->id;
            $page->parent_id = $request->parent_id ?? null;
            $page->is_category = 1;
            $page->save();


            KnowBaseModel::query()->where('book_id', $request->get('id'))->delete();

            $access = 0;
            if (
                count($request['who_can_read']) == 1
                && $request['who_can_read'][0]['id'] == 0
                && $request['who_can_read'][0]['type'] == 0
            ) {
                $access = 1;

            } else {
                $this->saveBookAccesses($request->get('id'), $request['who_can_read']);
            }

            if (
                count($request['who_can_edit']) == 1
                && $request['who_can_edit'][0]['id'] == 0
                && $request['who_can_edit'][0]['type'] == 0
            ) {
                $access = 2;
            } else {
                $this->saveBookAccesses($request->id, $request['who_can_edit'], 2);
            }

            $page->access = $access;
            $page->read_pairs = collect($request['who_can_read_pairs'])->toArray();
            $page->edit_pairs = collect($request['who_can_edit_pairs'])->toArray();
            $page->save();

        }

    }

    /**
     * Get favourite knowbases
     *
     */
    public function getFavourites(): array
    {
        $favourites = DB::table('user_starred_kbs')->where('user_id', Auth::id())->pluck('kb_id')->toArray();

        /** @var KnowBase $items */
        $items = KnowBase::query()->whereIn('id', $favourites)->select('id', 'title')->get();


        foreach ($items as $item) {
            $item->topParent_id = $item->getTopParentV2($item->id);
        }

        return [
            'items' => $items,
        ];
    }

    /**
     * Update access to knowbase
     * update knowbase
     */
    public function toggleFavourite(Request $request, KnowBase $kb): void
    {
        $favourite = DB::table('user_starred_kbs')->where('user_id', Auth::id())->where('kb_id', $kb->id)->first();
        if ($favourite && $request->toggle != 1) {
            DB::table('user_starred_kbs')->where('user_id', Auth::id())->where('kb_id', $kb->id)->delete();
        } elseif (!$favourite && $request->toggle == 1) {
            \DB::table('user_starred_kbs')->insert([
                'user_id' => Auth::id(),
                'kb_id' => $kb->id
            ]);
        }
    }

    /**
     * save knowbase accesses
     */
    private function saveBookAccesses($book_id, $items, $level = 1): void
    {
        foreach ($items as $key => $item) {
            if ($item['type'] == 1) $model = 'App\\User';
            if ($item['type'] == 2) $model = 'App\\ProfileGroup';
            if ($item['type'] == 3) $model = 'App\\Position';

            KnowBaseModel::create([
                'model_type' => $model,
                'model_id' => $item['id'],
                'book_id' => $book_id,
                'access' => $level
            ]);
        }
    }

    /**
     * update knowbase title and text
     */
    public function updatePage(Request $request, $id = null): void
    {
        /** @var KnowBase $page */
        $page = KnowBase::query()->find($request->id);
        if ($page) {
            $page->text = $request->text ?? '';

            $page->title = $request->title ?? 'Без названия';
            $page->editor_id = Auth::user()->id;

            $page->save();

            $this->notifyAboutChanges('Страница: ', $page);
        }
    }

    /**
     * check settings
     */
    private function canSaveWithoutTest(): bool
    {
        $setting = Setting::query()->where('name', 'allow_save_kb_without_test')->first();
        return $setting && $setting->value == 1;
    }

    /**
     * send notifications to users who has access to knowbase
     * if settings was set
     */
    private function notifyAboutChanges($text, KnowBase $page): void
    {
        $setting = Setting::query()->where('name', 'send_notification_after_edit')
            ->first();

        if ($setting && $setting->value == 1) {

            $TOP_parent = $this->getTopParent($page->id);

            if (!$TOP_parent) return;


            if ($TOP_parent->access == 1 || $TOP_parent->access == 2) {
                $users = User::query()->with('user_description')
                    ->whereHas('user_description', function ($query) {
                        $query->where('is_trainee', 0);
                    })
                    ->get('id')
                    ->pluck('id')
                    ->toArray();
            } else {
                $users = $TOP_parent->getUsersWithAccess();
            }


            $message = 'База знаний: <b>' . $TOP_parent->title . '</b><br><b>' . $text . ':</b> ';
            $message .= '<a href="/kb?s=' . $TOP_parent->id . '&b=' . $page->id . '" target="_blank">' . $page->title . '</a>';

            $today = now()->toDateString();

            foreach ($users as $key => $user_id) {
                $existingNotification = UserNotification::query()
                    ->where('user_id', $user_id)
                    ->where('title', 'Изменения в базе знаний')
                    ->whereDate('created_at', $today)
                    ->first();

                if ($existingNotification) {
                    $existingNotification->update([
                        'message' => $existingNotification->message . "<br><br>" . $message, // Append the new message
                    ]);
                } else {
                    UserNotification::query()->create([
                        'user_id' => $user_id,
                        'about_id' => 0,
                        'title' => 'Изменения в базе знаний',
                        'group' => now(),
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    /**
     * order of knowbases
     */
    public function saveOrder(Request $request, $id = null): void
    {

        $page = KnowBase::query()->find($request->id);
        if ($page) {
            $page->parent_id = $request->parent_id;
            $page->order = $request->order;
            $page->save();
        }

        $pages = KnowBase::query()->where('parent_id', $request->parent_id)
            ->where('id', '!=', $request->id)
            ->orderBy('order', 'asc')
            ->get();

        $order = 0;
        foreach ($pages as $page) {
            if ($order == $request->order) {
                $order++;
            }
            $page->order = $order;
            $page->save();
            $order++;
        }
    }

    /**
     * new knowbase page
     */
    public function createPage(Request $request): KnowBase
    {
        $kb = KnowBase::query()->where('parent_id', $request->id)->orderBy('order', 'desc')->first();
        $order = $kb ? $kb->order + 1 : 0;

        $kb = KnowBase::create([
            'title' => 'Новая страница',
            'text' => '',
            'user_id' => Auth::user()->id,
            'editor_id' => Auth::user()->id,
            'order' => $order,
            'parent_id' => $request->id,
            'hash' => md5(uniqid() . mt_rand()),
            'is_deleted' => 0,
        ]);

        $kb->children = [];
        $kb->questions = [];
        $kb->parent_id = null;

        $this->notifyAboutChanges('Добавлена новая страница: ', $kb);

        return $kb;
    }

    /**
     * create parent knowbase (book)
     */
    public function addSection(Request $request): KnowBase
    {
        $request->validate([
            'parent_id' => 'nullable|int'
        ]);

        $kb = KnowBase::whereNull('parent_id')->orderBy('order', 'desc')->first();

        return KnowBase::create([
            'title' => $request->name,
            'text' => '',
            'user_id' => Auth::user()->id,
            'editor_id' => Auth::user()->id,
            'order' => $kb ? $kb->order + 1 : 0,
            'parent_id' => $request->parent_id ?? null,
            'is_category' => 1,
            'hash' => 'cat',
            'is_deleted' => 0,
        ]);

    }

    /**
     * delete parent knowbase (book)
     */
    public function deleteSection(Request $request): void
    {
        $kb = KnowBase::find($request->id);
        if ($kb) $kb->delete();
    }

    /**
     * delete child knowbase
     */
    public function deletePage(Request $request): void
    {
        $kb = KnowBase::find($request->id);
        if ($kb) {
            $children = KnowBase::getAllChildrenIdsByKbId($kb->id);
            KnowBase::query()->whereIn('id', $children)->delete();
            $kb->delete();
        }

    }

    /**
     * restore parent knowbase
     */
    public function restoreSection(Request $request): void
    {
        $kb = KnowBase::onlyTrashed()->find($request->id);
        if ($kb) {
            $kb->restore();
            $kb->is_category = 1;
            $kb->save();
        }
    }

    /**
     * save test questions for knowbase page
     * @return array of ids
     */
    public function saveTest(Request $request): array
    {
        $ids = [];
        foreach ($request->questions as $key => $q) {
            $params = [
                'order' => 0,
                'page' => 0,
                'points' => $q['points'],
                'testable_id' => $request->id,
                'testable_type' => "App\Knowbase",
                'text' => $q['text'],
                'type' => $q['type'],
                'variants' => $q['variants'],
            ];

            if ($q['id'] != 0) {
                $testq = TestQuestion::find($q['id']);
                if ($testq) {
                    $testq->update($params);
                }
                $ids[] = $q['id'];

            } else {
                $q = TestQuestion::create($params);
                $ids[] = $q->id;
            }
        }

        // count pass grade
        $pass_grade = $request->pass_grade;
        if ($pass_grade > count($request->questions)) $pass_grade = count($request->questions);

        $book = KnowBase::withTrashed()->find($request->id);
        $book->pass_grade = $pass_grade;
        $book->save();

        return $ids;
    }

    /**
     * access editor for knowbase
     */
    public function getAccess(Request $request): array
    {
        $selected_all_badge = [ // All badge in superselect.vue
            'id' => 0,
            'type' => 0,
            'name' => 'Все',
        ];

        if (is_array($request['id'])) {
            $books = KnowBase::withTrashed()->whereIn('id', $request['id'])->get();

            $permissions = [];
            foreach ($books as $book) {
                $permissions[$book->id] = [
                    'who_can_read' => $book->access == 1 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($book->id, 'read'),
                    'who_can_edit' => $book->access == 2 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($book->id, 'edit'),
                    'who_can_read_pairs' => $book->read_pairs,
                    'who_can_edit_pairs' => $book->edit_pairs
                ];
            }

            return $permissions;
        }

        $book = KnowBase::withTrashed()->find($request->id);

        return [
            'who_can_read' => $book->access == 1 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($request->id, 'read'),
            'who_can_edit' => $book->access == 2 ? [$selected_all_badge] : $this->getWhoCanReadOrEdit($request->id, 'edit'),
            'who_can_read_pairs' => $book->read_pairs,
            'who_can_edit_pairs' => $book->edit_pairs
        ];
    }

    /**
     * users who has access to knowbase
     */
    private function getWhoCanReadOrEdit($book_id, $access = 'read'): array
    {
        $can = [];

        $items = KnowBaseModel::where([
            'book_id' => $book_id,
            'access' => $access == 'edit' ? 2 : 1
        ])->get();

        foreach ($items as $key => $item) {

            $arr = [];
            $arr['id'] = $item['model_id'];

            if ($item->model_type == 'App\\User') {
                $arr['type'] = 1;
                $user = User::withTrashed()->find($item->model_id);
                if (!$user) continue;
                $arr['name'] = $user->last_name . ' ' . $user->name;
            }

            if ($item->model_type == 'App\\ProfileGroup') {
                $arr['type'] = 2;
                $group = ProfileGroup::find($item->model_id);
                if (!$group) continue;
                $arr['name'] = $group->name;
            }

            if ($item->model_type == 'App\\Position') {
                $arr['type'] = 3;
                $pos = Position::find($item->model_id);
                if (!$pos) continue;
                $arr['name'] = $pos->position;
            }

            $can[] = $arr;
        }

        return $can;

    }

    public function uploadimages(Request $request)
    {
        $image = $request->file('attachment');
        $image_name = time() . '.' . $image->getClientOriginalExtension();
        $image->move("bpartners", $image_name);

        return json_encode(array('location' => "/bpartners/" . $image_name));
    }

    public function uploadaudio(Request $request)
    {

        $audio = $request->file('attachment');
        $audio_name = time() . '.' . $audio->getClientOriginalExtension();
        $audio->move("bpartners/audio/", $audio_name);

        return json_encode(array('location' => "/bpartners/audio/" . $audio_name));
    }

}
