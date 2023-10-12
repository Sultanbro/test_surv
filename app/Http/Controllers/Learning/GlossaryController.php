<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\GlossaryModel;
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

    private function saveGlossaryAccesses($items)
    {

    }
}
