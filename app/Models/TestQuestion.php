<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $table = 'test_questions';

    public $timestamps = true;

    protected $appends = ['editable', 'checked'];

    protected $casts = [
        'variants' => 'array',
    ];

    protected $fillable = [
        'type',
        'text',
        'variants',
        'order',
        'points',
        'page', // only for books
        'testable_id',
        'testable_type',
    ];

    CONST TYPE_ABC = 0;
    CONST TYPE_OPEN = 1;


    public function testable()
    {
        return $this->morphTo();
    }

    public function getEditableAttribute()
    {
        return false;
    }

    public function getCheckedAttribute()
    {
        return false;
    }

    public function result()
    {
        return $this->hasOne('App\Models\TestResult');
    }
}
