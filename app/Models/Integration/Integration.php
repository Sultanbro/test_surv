<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $reference
 * @property string $data
 */
class Integration extends Model
{
    use HasFactory;

    protected $table = 'integrations';

    protected $fillable = [
        'reference',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
