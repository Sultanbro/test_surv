<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\GlossaryModel;
use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Http\Request;
use App\Models\GlossaryWord as Word;

class GlossaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function get(Request $request)
    {
        return Word::orderBy('word')->get();
    }


    /**
     * @param Request $request
     *
     * @return void
     */
    public function delete(Request $request)
    {
        Word::where('id', $request->id)->delete();
    }


    /**
     * @param Request $request
     *
     * @return int
     */
    public function save(Request $request)
    {
        $input = [
            'word' => $request->word['word'],
            'definition' => $request->word['definition'],
        ];

        if ($request->id == 0) {
            $word = Word::create($input);
        } else {
            $word = Word::find($request->id);
            $word->update($input);
        }

        return $word ? $word->id : 0;
    }

    public function updateAccess(Request $request)
    {
        GlossaryModel::query()->truncate();

        foreach ($request['who_can_edit'] as $item) {
            if ($item['type'] == 1) $model = 'App\\User';
            if ($item['type'] == 2) $model = 'App\\ProfileGroup';
            if ($item['type'] == 3) $model = 'App\\Position';

            GlossaryModel::create([
                'model_type' => $model,
                'model_id' => $item['id'],
                'access' => GlossaryModel::EDIT_ACCESS
            ]);
        }
    }

    public function getAccess(Request $request) : array
    {
        $can = [];

        $items = GlossaryModel::where([
            'access' => GlossaryModel::EDIT_ACCESS
        ])->get();

        foreach ($items as $key => $item) {

            $arr = [];
            $arr['id'] = $item['model_id'];

            if($item->model_type == 'App\\User') {
                $arr['type'] = 1;
                $user = User::withTrashed()->find($item->model_id);
                if(!$user) continue;
                $arr['name'] = $user->last_name . ' ' . $user->name;
            }

            if($item->model_type == 'App\\ProfileGroup') {
                $arr['type'] = 2;
                $group = ProfileGroup::find($item->model_id);
                if(!$group) continue;
                $arr['name'] = $group->name;
            }

            if($item->model_type == 'App\\Position') {
                $arr['type'] = 3;
                $pos = Position::find($item->model_id);
                if(!$pos) continue;
                $arr['name'] = $pos->position;
            }

            $can[] = $arr;
        }

        return [
            'who_can_edit' => $can,
        ];
    }
}
