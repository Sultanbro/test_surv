<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlossaryWord as Word;
use App\User;

class GlossaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * 
     * @return Collection
     */
    public function get(Request $request)
    {
        return Word::orderBy('word')->get();
    }   


    /**
     * @param Request $request
     * 
     * @return Collection
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

        if($request->id == 0){
            $word = Word::create($input);
        } else {
            $word = Word::find($request->id);
            $word->update($input);
        }
        
        return $word  ? $word->id : 0;
    }
}
