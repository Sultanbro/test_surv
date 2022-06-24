<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlossaryWord extends Model
{
    use HasFactory;

    protected $table = 'glossary_words';

    public $timestamps = true;

    protected $fillable = [
        'word',
        'definition',
    ];

}
