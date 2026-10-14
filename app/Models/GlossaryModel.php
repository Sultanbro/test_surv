<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlossaryModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'glossary_model';

    protected $fillable = [
        'model_id',
        'model_type',
        'access',
    ];

    const EDIT_ACCESS = 2;
}
