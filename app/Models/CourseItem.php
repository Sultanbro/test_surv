<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Videos\VideoPlaylist;
use App\Models\Books\Book;
use App\Models\Videos\Video;
use App\KnowBase;

class CourseItem extends Model
{
    protected $table = 'course_items';

    public $timestamps = true;

    protected $fillable = [
        'course_id',
        'item_id',
        'item_model',
        'order',
        'title',
        'items',
    ];

    // item_model
    CONST MODEL_VIDEO = 'video';
    CONST MODEL_KB = 'kb';
    CONST MODEL_BOOK = 'book';


    public function elements()
    {
        return $this->hasMany(CourseItemModel::class, 'course_item_id')->orderBy('id');
    }

    

    /**
     * NUmber of elements in CourseItem
     */
    public function countItems()
    {   
        if($this->isVideo()) {
            $pl = VideoPlaylist::with('videos')->find($this->item_id);
            return $pl ? $pl->videos->count() : 0;
        }

        if($this->isBook()) {
            $book = Book::with('questions')->find($this->item_id);
            return $book ? $book->questions->groupBy('page')->count() : 0;
        }

        if($this->isKnowbase()) {
            $steps = [];

            $kb = KnowBase::where('id', $this->item_id)->first();
            if(!$kb) return 0;

            KnowBase::getArray($steps, $kb);
            return count($steps);
        }
    }

    public function isVideo() {
        return $this->item_model == 'App\Models\Videos\Video';
    }

    public function isBook() {
        return $this->item_model == 'App\Models\Books\Book';
    }

    public function isKnowbase() {
        return $this->item_model == 'App\KnowBase';
    }

    public function getNextElement($last_id)
    {
        if($this->isVideo()) {
            $pl = VideoPlaylist::with('groups')->find($this->item_id);
            $no_group_videos = Video::where('group_id', 0)->where('playlist_id', $pl->id)->with('questions')->get();

            if($no_group_videos->count() > 0) {
                $pl->groups->prepend(['title' => 'Без группы', 'id' => 0, 'videos' => $no_group_videos, 'opened' => false]);
            }

            $last_item = null;
            $next_item = null;
            foreach ($pl->groups as $key => $g) {

                if($last_item) {
                    $el = $g->videos->first();
                    if($el) {
                        $next_item = $el;
                        break;
                    } else {
                        foreach ($g->children as $ckey => $c) {
                          
                            if(count($c['videos']) > 0) {
                                $next_item = $c['videos'][0];
                                break;
                            }
                            
                        }
                    }
                    
                }

                $el = collect($g['videos'])->where('id', $last_id)->first();
                if($el) {
                    $last_item = $el;
                    $break = false;
                    foreach (collect($g['videos']) as $vkey => $video) {
                        if($break) {
                            $next_item = $video;
                            break;
                        }
                        if($video->id == $last_item->id) {
                            $break = true;
                        }
                    }
                } else {
                    foreach ($g->children as $ckey => $c) {
                        $el = $c->videos->where('id', $last_id)->first();
                        if($el) {
                            $last_item = $el;

                            $break = false;
                            foreach ($c['videos'] as $vkey => $video) {
                                if($break) {
                                    $next_item = $video;
                                    break;
                                }
                                if($video['id'] == $last_item->id) {
                                    $break = true;
                                }
                            }

                        }
                    }
                }

                
            }

            return $next_item ? $next_item->id : null;
		
        }

        if($this->isBook()) {
            $book = Book::with('questions')->find($this->item_id);
            if(!$book) return null;

            $el = $book->questions->where('page', '>' , $last_id)->first();
            return $el ? $el->page : null;   
        }

        if($this->isKnowbase()) {

            $kb = KnowBase::where('id', $this->item_id)->with('children')->first();

            $next_item = null;
            $arr = [];

            if($kb) $this->getChildItems($kb->children, $arr);

            $break = false;
            foreach ($arr as $key => $item) {
                if($break) {
                    $next_item = $item;
                    break;
                }

                if($last_id == $item) {
                    $break = true;
                }
            }

            return $next_item;
        }
    }

    private function getChildItems($items, &$arr)
    {
        foreach ($items as $key => $c) {
            $arr[] = $c->id;
            $this->getChildItems($items->children, $arr);
        }
    }

}
