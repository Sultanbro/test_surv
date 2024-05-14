<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowBaseModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'knowbase_model';

    protected $fillable = [
        'book_id',
        'model_id',
        'model_type',
        'access',
    ];
}
